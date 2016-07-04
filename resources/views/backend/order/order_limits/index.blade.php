@extends('backend.master.layout')

@section('breadcrumb')
    <li>
        <span>Sales</span>
        <i class="fa fa-circle"></i>
    </li>

    <li>
        <span>{{ \Kommercio\Models\Order\OrderLimit::getTypeOptions($type) }} Order Limit</span>
    </li>
@stop

@section('content')
    <div class="col-md-12">
        <div class="portlet light portlet-fit portlet-datatable bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject sbold uppercase"> {{ \Kommercio\Models\Order\OrderLimit::getTypeOptions($type) }} Order Limit </span>
                </div>
                <div class="actions">
                    @can('access', ['create_order_limit'])
                    <a href="{{ route('backend.order_limit.create', ['type' => $type, 'backUrl' => Request::fullUrl()]) }}" class="btn btn-sm btn-info">
                        <i class="fa fa-plus"></i> Add </a>
                    @endcan
                </div>
            </div>

            <div class="portlet-body">
                <table class="table table-striped table-bordered table-advance">
                    <thead>
                    <tr>
                        <th>{{ \Kommercio\Models\Order\OrderLimit::getTypeOptions($type) }}</th>
                        <th>Limit</th>
                        <th>Type</th>
                        <th>Date From</th>
                        <th>Date To</th>
                        <th>Active</th>
                        <th style="width: 20%;">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orderLimits as $orderLimit)
                        <tr>
                            <td>
                                <ul>
                                    @foreach($orderLimit->getItems() as $item)
                                    <li>{{ $item->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                {{ $orderLimit->limit+0 }}
                            </td>
                            <td>
                                {{ \Kommercio\Models\Order\OrderLimit::getLimitTypeOptions($orderLimit->limit_type) }}
                            </td>
                            <td>
                                {{ $orderLimit->date_from?$orderLimit->date_from->format('d M Y H:i'):null }}
                            </td>
                            <td>
                                {{ $orderLimit->date_to?$orderLimit->date_to->format('d M Y H:i'):null }}
                            </td>
                            <td> <i class="fa {{ $orderLimit->active?'fa-check text-success':'fa-remove text-danger' }}"></i> </td>
                            <td class="text-center">
                                {!! Form::open(['route' => ['backend.order_limit.delete', 'id' => $orderLimit->id]]) !!}
                                <div class="btn-group btn-group-sm">
                                    @can('access', ['edit_order_limit'])
                                    <a class="btn btn-default" href="{{ route('backend.order_limit.edit', ['id' => $orderLimit->id, 'backUrl' => Request::fullUrl()]) }}"><i class="fa fa-pencil"></i> Edit</a>
                                    @endcan

                                    @can('access', ['delete_order_limit'])
                                    <button class="btn btn-default" data-toggle="confirmation" data-original-title="Are you sure?" title=""><i class="fa fa-trash-o"></i> Delete</button>
                                    @endcan
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop