<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Complete Order</h4>
</div>

{!! Form::open(['route' => ['backend.sales.order.process', 'process' => 'completed', 'id' => $order->id]]) !!}
<div class="modal-footer text-center">
    <div class="pull-left">
        <div class="checkbox-list">
            <label class="checkbox-inline">
                {!! Form::checkbox('send_notification', 1, true) !!} Send email notification to customer
            </label>
        </div>
    </div>

    <div class="pull-right">
        <button class="btn btn-primary"><i class="fa fa-check"></i> Confirm </button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-remove"></i> Cancel</button>
        {!! Form::hidden('backUrl', $backUrl) !!}
    </div>
</div>
{!! Form::close() !!}