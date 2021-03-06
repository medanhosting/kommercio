<?php

namespace Kommercio\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request as RequestFacade;
use Kommercio\Facades\ProjectHelper as ProjectHelperFacade;
use Kommercio\Facades\CurrencyHelper as CurrencyHelperFacade;
use Illuminate\Http\Request;
use Kommercio\Models\CMS\BannerGroup;
use Kommercio\Models\CMS\Block;
use Kommercio\Models\CMS\Menu;
use Kommercio\Models\CMS\MenuItem;
use Kommercio\Models\Order\Order;
use Kommercio\Models\Product;
use Kommercio\Models\UrlAlias;

class FrontendHelper
{
    private $_currentOrder;

    private $_miniAliasCache;

    public function getAlias($internal_path)
    {
        if($internal_path == '/'){
            return $this->getHomeUrl();
        }

        $locale = App::getLocale();

        if(isset($this->_miniAliasCache[$internal_path.':'.$locale])){
            $path = $this->_miniAliasCache[$internal_path.':'.$locale];
        }else{
            $urlAlias = UrlAlias::where('internal_path', $internal_path)
                ->where('locale', $locale)
                ->first();

            if($urlAlias){
                $path = $urlAlias->external_path;
            }else{
                $path = $internal_path;
            }

            $this->_miniAliasCache[$internal_path.':'.$locale] = $path;
        }

        return $path;
    }

    /*
     * Alias of getUrl
     */
    public function get_url($internal_path, $params = [], $secure = null)
    {
        return $this->getUrl($internal_path, $params, $secure);
    }

    public function getUrl($internal_path, $params = [], $secure = null)
    {
        $path = $this->getAlias($internal_path);

        return url($path, $params, $secure);
    }

    public function getCurrentUrlWithQuery($query = [])
    {
        $path = RequestFacade::path();

        $query = array_replace_recursive(RequestFacade::query(), $query);

        $this->clearEmpty($query);

        return $this->get_url($path).(!empty($query)?'?'.http_build_query($query):'');
    }

    public function getHomeUrl()
    {
        //$homePath = config('project.home_uri');

        return url('/');
    }

    /*
     * Alias of getHomeUrl
     */
    public function get_home_url()
    {
        return $this->getHomeUrl();
    }

    public function isHomepage()
    {
        $requestUri = RequestFacade::getRequestUri();
        $requestUri = urldecode(substr($requestUri,1));

        $homePath = config('project.home_uri');

        return $requestUri == $homePath;
    }

    public function pathIsHere($path)
    {
        $currentPath = substr(RequestFacade::getPathInfo(), 1);

        return $currentPath == $path;
    }

    public function pathIsDescendant($path)
    {
        if(is_array($path)){
            $paths = $path;
        }else{
            $paths = [$path];
        }

        $currentPath = substr(RequestFacade::getPathInfo(), 1);

        foreach($paths as $path){
            if(!empty($path) && strpos($currentPath.'/', $path) === 0){
                return true;
            }
        }

        // If couldn't find anything by internal path, we turn to external path
        // We have to use global $_SERVER['REQUEST_URI'] because Request::getRequestUri has been changed in the UrlAlias middleware
        $requestUri = $_SERVER['REQUEST_URI'];

        $baseUrl = RequestFacade::getBaseUrl().'/';

        if (substr($requestUri, 0, strlen($baseUrl)) == $baseUrl) {
            $requestUri = substr($requestUri, strlen($baseUrl));
        }

        foreach($paths as $path){
            $externalPath = UrlAlias::where('internal_path', $path)->first();

            if($externalPath){
                if(strpos($requestUri, $externalPath->external_path.'/') === 0){
                    return true;
                }
            }
        }

        return false;
    }

    public function checkCatalogSort($sort_by, $sort_dir)
    {
        return RequestFacade::get('sort_by') == $sort_by && RequestFacade::get('sort_dir') == $sort_dir;
    }

    //Menus
    public function getRootMenuItems($menu_slug)
    {
        $menuItems = [];

        if(is_array($menu_slug)){
            $menu_slugs = $menu_slug;
        }else{
            $menu_slugs = [$menu_slug];
        }

        foreach($menu_slugs as $menu_slug){
            $menu = Menu::where('slug', $menu_slug)->first();
            $rootMenuItems = $menu->rootMenuItems->filter(function($value, $key){
                return $value->active;
            });

            $menuItems += ($rootMenuItems->count() > 0)?$rootMenuItems->all():[];
        }

        $menuItems = new Collection($menuItems);

        return $menuItems;
    }

    public function getMenuItemSiblings($path, $menu_slug = null, $level = 1)
    {
        if($menu_slug){
            $menu = Menu::where('slug', $menu_slug)->firstOrFail();
        }else{
            if($menuItem = MenuItem::whereTranslation('url', $path)->first()){
                $menu = $menuItem->menu;
            }else{
                return collect([]);
            }
        }

        $trails = $menu->getTrails($path);

        return isset($trails[$level-1])?$menu->getMenuItemSiblings($trails[$level-1]):collect([]);
    }

    public function getMenuItemChildren($menu_item_id, $menu_slug)
    {
        $menu = Menu::where('slug', $menu_slug)->firstOrFail();
        $menuItem = $menu->menuItems()->active()->findOrFail($menu_item_id);

        return $menuItem->children;
    }

    //Banners
    public function getBanners($banner_group_slug)
    {
        $bannerGroup = BannerGroup::with('banners')->where('slug', $banner_group_slug)->first();

        $banners = $bannerGroup?$bannerGroup->getBanners():collect([]);

        return $banners;
    }

    //Block
    public function getBlock($machine_name)
    {
        $block = Block::getBySlug($machine_name);

        if ($block && $block->active) {
            return $block;
        }

        return null;
    }

    //Products
    public function getNewProducts($take = null, $criteria = [])
    {
        $newItems = [];

        $qb = Product::isNew()->active()->catalogVisible()->productEntity()->orderBy('created_at', 'DESC');

        if($take){
            $newItems = $qb->take($take);
        }

        $newItems = $qb->get();

        return $newItems;
    }

    //Cart
    public function getSoonestDeliveryDay($format='Y-m-d')
    {
        $now = Carbon::now();

        $soonest = ProjectHelperFacade::getConfig('soonest_delivery_days');

        $now->modify('+'.$soonest.' days');

        return $now->format($format);
    }

    public function getCurrentOrder($context=null)
    {
        $cookieKey = ProjectHelperFacade::getConfig('project_machine_name', 'kommercio').'_order_id';

        if(Cookie::has($cookieKey) && !isset($this->_currentOrder)){
            $order = Order::where('id', Cookie::get($cookieKey))
                ->where('status', Order::STATUS_CART)
                ->first();

            if($order){
                $refreshInterval = $order->created_at->modify('+12 hours');
                if($order->created_at && $refreshInterval->lt(Carbon::now())){
                    $order->reset();
                }
            }

            $this->_currentOrder = $order;
        }

        if(empty($this->_currentOrder)){
            $store = ProjectHelperFacade::getActiveStore();

            $order = new Order();
            $order->store()->associate($store);
            $order->ip_address = RequestFacade::ip();
            $order->user_agent = RequestFacade::header('User-Agent');
            $order->status = Order::STATUS_CART;
            $order->currency = CurrencyHelperFacade::getCurrentCurrency()['code'];

            $this->_currentOrder = $order;
        }

        //Tie order to logged in customer
        $user = Auth::user();
        if($user && $user->customer){
            $this->_currentOrder->customer()->associate($user->customer);
        }

        if($context == 'save'){
            if (empty($this->_currentOrder->reference)) {
                $this->_currentOrder->reference = microtime(true);
            }

            $this->_currentOrder->save();

            // Assign ID to reference as surrogate
            if (!$this->_currentOrder->reference || !$this->_currentOrder->order_number) {
                $this->_currentOrder->reference = $this->_currentOrder->id;
            }

            $cookie = Cookie::make($cookieKey, $this->_currentOrder->id, 25200);
            Cookie::queue($cookie);
        }

        return $this->_currentOrder;
    }

    public function generatePageTitle($text)
    {
        $isHomepage = FrontendHelper::isHomepage();

        if($isHomepage){
            $title = $text;
        }else{
            $title = $text.' - '.ProjectHelperFacade::getClientName();
        }

        return $title;
    }

    //Protected
    protected function clearEmpty(&$array) {
        foreach ( $array as $key => $item ) {
            is_array ( $item ) && $array [$key] = $this->clearEmpty ( $item );
            if (empty ( $array [$key] ))
                unset ( $array [$key] );
        }
        return $array;
    }
}