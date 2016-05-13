<div class="row">
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-user"></i>
                    <span class="caption-subject">Customer Information</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>
                            <input id="existing_customer"
                                   name="existing_customer"
                                   type="text"
                                   value="{{ old('existing_customer') }}"
                                   class="form-control"
                                   data-typeahead_remote="{{ route('backend.customer.autocomplete') }}"
                                   data-typeahead_display="email"
                                   data-typeahead_label="name"
                                   placeholder="Search Customer">
                        </div>

                        <hr/>
                    </div>
                </div>

                <div id="billing-information-wrapper" data-profile_source="{{ route('backend.sales.order.copy_customer_information', ['type' => 'profile']) }}">
                    @include('backend.order.customer_information', ['type' => 'profile'])
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-truck"></i>
                    <span class="caption-subject">Shipping Information</span>
                </div>
                <div class="actions">
                    <a href="javascript:;" id="shipping-copy-btn" class="btn btn-warning btn-sm">Same as Customer</a>
                </div>
            </div>
            <div class="portlet-body" id="shipping-information-wrapper" data-profile_source="{{ route('backend.sales.order.copy_customer_information', ['type' => 'shipping_profile']) }}">
                @include('backend.order.customer_information', ['type' => 'shipping_profile'])
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="portlet box blue-hoki">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="caption-subject">Order Content</span>
                </div>
                <div class="actions">
                    <a href="javascript:;" id="order-clear" class="btn btn-default btn-sm">Clear</a>
                </div>
            </div>
            <div class="portlet-body" id="order-content-wrapper">
                <table id="line-items-table" class="table table-hover table-bordered table-striped">
                    <thead>
                    <tr>
                        <th> Item </th>
                        <th style="width: 20%;"> Original Price </th>
                        <th style="width: 25%;"> Net Price </th>
                        <th style="width: 5%;"> Quantity </th>
                        <th> Total </th>
                        <th>  </th>
                    </tr>
                    </thead>
                    <tbody>
                        @include('backend.order.line_items.form.product')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6"></div>
    <div class="col-md-6">
        <div class="well">
            <div class="row static-info align-reverse">
                <div class="col-md-8 name"> Sub Total: </div>
                <div class="col-md-3 value"> $1,124.50 </div>
            </div>
            <div class="row static-info align-reverse">
                <div class="col-md-8 name"> Shipping: </div>
                <div class="col-md-3 value"> $40.50 </div>
            </div>
            <div class="row static-info align-reverse">
                <div class="col-md-8 name"> Grand Total: </div>
                <div class="col-md-3 value"> $1,260.00 </div>
            </div>
        </div>
    </div>
</div>

{!! Form::hidden('store_id', ProjectHelper::getActiveStore()->id) !!}

@section('bottom_page_scripts')
    @parent

    <script>
        global_vars.product_line_item = '{{ route('backend.sales.order.line_item.row', ['type' => 'product']) }}';
    </script>

    <script src="{{ asset('backend/assets/scripts/pages/order_form.js') }}" type="text/javascript"></script>
@stop