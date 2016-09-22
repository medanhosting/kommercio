(function($) {

    $.checkoutForm = function(element, options) {

        var defaults = {
            errorMessageClass: 'messages alert alert-danger'
        }

        var plugin = this;

        plugin.settings = {}

        var $element = $(element),
            element = element;

        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);

            initComponent(element);
        }

        plugin.processCheckout = function($form, $process)
        {
            KommercioFrontend.clearErrors(element);

            $form.triggerHandler('validate_step_submit', [$process, checkoutData]);

            $element.append('<div class="loading-overlay" />');

            if(checkoutData.run_flag){
                plugin.submitCheckoutForm($form, $process);
            }else{
                checkoutData.run_flag = true;
                $element.find('.loading-overlay').remove();
            }
        };


        plugin.submitCheckoutForm = function($form, $process){
            $.ajax($form.attr('action'), {
                method: 'POST',
                data: $form.serialize() + '&process=' + $process,
                success: function(data){
                    var $html = null;

                    for(var i in data.data){
                        $html = $(data.data[i]);

                        if(data.step == 'complete'){
                            $('#ajax-meat').replaceWith($html);
                        }else{
                            $('#'+ i +'-wrapper', element).html($html);
                        }

                        initComponent($('#'+ i +'-wrapper', element));
                        checkoutData.step = data.step;
                    }
                },
                complete: function(){
                    $element.find('.loading-overlay').remove();

                },
                error: function(data){
                    if($process != 'place_order'){
                        for(var i in data.responseJSON){
                            KommercioFrontend.addError(i, data.responseJSON[i][0], element);
                        }
                    }else{
                        $('#checkout_summary-wrapper .messages', element).remove();
                        $('#checkout_summary-wrapper .update-cart', element).prepend('<div class="' + plugin.settings.errorMessageClass + '"></div>');

                        for(var i in data.responseJSON){
                            $('#checkout_summary-information .messages', element).append('<div>' + data.responseJSON[i][0] + '</div>');
                        }
                    }
                }
            });
        }

        var initComponent = function(context)
        {
            handleAddressOptions(context);
            handleStepButton(context);
            handleShippingMethod(context);
            handlePaymentMethod(context);
            if(global_vars.enable_delivery_date){
                handleAvailability(context);
            }
            handleSavedAddress(context);
        }

        var handleSavedAddress = function(context)
        {
            $('#shipping-profile-select', context).on('change', function(){
                var $form = $(this).parents('form');

                plugin.processCheckout($form, 'change');
            });
        }

        var handleAddressOptions = function(context)
        {
            var $shippingMethodSelect = $('#shipping_method', context);
            var $countrySelect = $('#country-select', context);
            var $stateSelect = $('#state-select', context);
            var $citySelect = $('#city-select', context);
            var $districtSelect = $('#district-select', context);

            $countrySelect.on('change', function(e){
                $.ajax(global_vars.base_path + '/address/state/options', {
                    'data' : 'parent=' + $(e.target).val(),
                    'dataType' : 'json',
                    'success' : function(data){
                        var $options = KommercioFrontend.selectHelper.convertToOptions(data, 'State');

                        $stateSelect.html($options);

                        $stateSelect.trigger('change');

                        delete $options;
                    }
                });
            });

            $stateSelect.on('change', function(e){
                $.ajax(global_vars.base_path + '/address/city/options', {
                    'data' : 'parent=' + $(e.target).val(),
                    'dataType' : 'json',
                    'success' : function(data){
                        var $options = KommercioFrontend.selectHelper.convertToOptions(data, 'City');

                        $citySelect.html($options);

                        $citySelect.trigger('change');

                        delete $options;
                    }
                });
            });

            $citySelect.on('change', function(e){
                $.ajax(global_vars.base_path + '/address/district/options', {
                    'data' : 'parent=' + $(e.target).val(),
                    'dataType' : 'json',
                    'success' : function(data){
                        var $options = KommercioFrontend.selectHelper.convertToOptions(data, 'District');

                        $districtSelect.html($options);

                        $districtSelect.trigger('change');

                        delete $options;
                    }
                });
            });

            if($stateSelect.find('option').length < 2){
                $countrySelect.trigger('change');
            }

            if($citySelect.find('option').length < 2){
                $stateSelect.trigger('change');
            }

            if($districtSelect.find('option').length < 2){
                $citySelect.trigger('change');
            }
        }

        var handleStepButton = function(context)
        {
            $('.next-step-btn', context).each(function(idx, obj){
                $(obj).on('click', function(e){
                    e.preventDefault();

                    var $form = $(obj).parents('form');

                    plugin.processCheckout($form, $(this).val());
                });
            });
        }

        var handleShippingMethod = function(context)
        {
            $('input[name="shipping_method"]', context).on('change', function(){
                var $form = $(this).parents('form');

                plugin.processCheckout($form, 'select_shipping_method');
            });
        }

        var handlePaymentMethod = function(context)
        {
            $('input[name="payment_method"]', context).on('change', function(){
                var $form = $(this).parents('form');

                plugin.processCheckout($form, 'select_payment_method');
            });
        }

        var $disabledDates = [];
        var $checkoutForm;

        var handleAvailability = function(context)
        {
            var datePickerClosed = true;
            var datepickerDate;
            $checkoutForm = $('#customer_information-wrapper form', context);

            //Availability from Calendar
            $datePicker = $('#delivery-datepicker', context);
            $datePicker.datepicker({
                startDate: global_vars.soonest_delivery_day,
                format: 'yyyy-mm-dd',
                autoclose: true,
                container: '#delivery-date-panel'
                /*,
                 beforeShowDay: function(e){
                 if($disabledDates.indexOf(e.getFullYear() + '-' + (e.getMonth()+1) + '-' + e.getDate()) > -1){
                 return false;
                 }
                 }
                 */
            }).on('show', function(e){
                if(datePickerClosed){
                    datepickerDate = $(e.target).datepicker('getDate');

                    if(datepickerDate == null){
                        datepickerDate = new Date();
                    }

                    handleOnChangeMonth(e, datepickerDate.getMonth(), datepickerDate.getFullYear(), datepickerDate.getDate());
                }

                datePickerClosed = false;
            }).on('hide', function(e){
                datePickerClosed = true;
            }).on('changeMonth', function(e){
                handleOnChangeMonth(e, e.date.getMonth(), e.date.getFullYear());
            });
            //End Availability from Calendar
        }

        var handleOnChangeMonth = function(e, month, year, date)
        {
            $('#delivery-date-panel .datepicker-days').css('visibility', 'hidden');
            $('#delivery-date-panel .datepicker').addClass('loading');

            $.ajax(global_vars.get_availability_calendar, {
                method: 'POST',
                data: $checkoutForm.serialize() + '&month=' + (month+1) + '&year=' + year,
                success: function(data){
                    $disabledDates = $.makeArray(data.disabled_dates);

                    $(e.target).datepicker('_process_options', {datesDisabled: $disabledDates});
                    $(e.target).datepicker('update', e.date);
                    //$(e.target).datepicker('moveMonth', e.date);
                    /*
                     if(typeof date !== 'undefined'){
                     $('#delivery_date', '#checkout-form').datepicker('update', date);
                     }else{
                     $('#delivery_date', '#checkout-form').datepicker('fill');
                     }
                     */
                },
                complete: function(){
                    $('#delivery-date-panel .datepicker-days').css('visibility', 'visible');
                    $('#delivery-date-panel .datepicker').removeClass('loading');
                }
            });
        }

        var checkoutData = {
            run_flag: true,
            step: null
        };

        plugin.init();

    }

    $.fn.checkoutForm = function(options) {

        return this.each(function() {
            if (undefined == $(this).data('checkoutForm')) {
                var plugin = new $.checkoutForm(this, options);
                $(this).data('checkoutForm', plugin);
            }
        });

    }

})(jQuery);