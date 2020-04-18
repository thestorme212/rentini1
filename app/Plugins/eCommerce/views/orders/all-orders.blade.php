<div class="container-fluid">
    <!-- /.row -->
    <div class="row">

        <div class="white-box">
            <div class="form-group row">
                <div class="col-md-9">
                    @if($request->search ?? false)
                        <h3>{{__('Search result for'). ' "'. $request->search . '"'}}  </h3>
                    @endif

                </div>

                <div class="col-md-3 col-xs-12 pull-right">
                    <form action="{{route('admin.products.orders.index')}}" method="GET">
                        <div class="row">

                            <div class="input-group">
                                <input name="search" value="{{$request->search ?? ''}}" type="text"
                                       class="form-control" placeholder="Search"/>
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>


                </div>

            </div>

            <div class="table-responsive">
                <table class="table product-overview" id="myTable">
                    <thead>
                    <tr>
                        <th>{{__("Order")}}</th>
                        <th>{{__("Photo")}}</th>
                        <th>{{__("Product")}}</th>
                        {{--<th>{{__("Quantity")}}</th>--}}
                        <th>{{__("Date")}}</th>
                        <th>{{__("Status")}}</th>
                        <th>{{__('admin.Actions')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($orders ?? false && $orders->count() > 0)

                        @foreach($orders as $order)

                            <tr class="media-item">

                                <td>
                                    <a href="{{route('admin.products.orders.edit',['order' => $order->id])}}">
                                        #{{$order->id}} {{$order->name}}</a>
                                </td>

                                <td>


                                    @if($order->items  && isset($order->items->first()->product))
                                        <img src="{{the_image_url($order->items->first()->product->img,'thumbnail-70x70')}}"
                                             alt="{{$order->items->first()->product->title}}" width="80">
                                    @endif
                                </td>
                                <td>
                                    @if($order->items  && isset($order->items->first()->product))
                                    <a href="{{route('admin.products.edit',['product' => $order->items->first()->product->id])}}">
                                    {{$order->items->first()->product->title}}
                                    </a>
                                    @endif
                                </td>
                                {{--<td>{{__("20")}}</td>--}}
                                <td>{{$order->created_at}}</td>
                                <td><span class="label label-success font-weight-100">{{$order->status}}</span></td>
                                <td>
                                    <a href="{{  route('admin.products.orders.edit',['order' => $order->id])  }}"
                                       class="text-inverse p-r-10" data-toggle="tooltip"
                                       title="{{__('admin.Edit')}}"><i class="ti-marker-alt"></i></a>

                                    <a
                                            href="javascript:void(0)"
                                            class="text-inverse delete_post"
                                            title="{{__('admin.Delete')}}"
                                            data-id="{{$order->id}}"
                                            data-toggle="tooltip"><i
                                                class="ti-trash"></i></a></td>
                            </tr>

                        @endforeach
                    @else


                    @endif


                    </tbody>
                </table>
                @if($orders->isEmpty())
                    <h6>{{__('admin.Nothing found...')}}</h6>
                @endif
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <ul class="pagination">
                    @if($orders->currentPage() !== 1)
                        <li class="disabled"><a href="{{$orders->url(($orders->currentPage() - 1))}}">
                                <i class="fa fa-angle-double-left"></i></a></li>

                    @endif

                    @for($i = 1; $i <= $orders->lastPage(); $i++)
                        @if($orders->currentPage() == $i)

                            <li class="active"><a href="#">{{ $i }}
                                    <span class="sr-only"></span></a>
                            </li>
                        @else

                            <li><a href="{{ $orders->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if($orders->currentPage() !== $orders->lastPage())

                        <li><a href="{{ $orders->url(($orders->currentPage() + 1)) }}"> <i
                                        class="fa fa-angle-double-right"></i></a></li>
                    @endif

                </ul>
            </div>
            <!-- /Pagination -->

        </div>
    </div>

    <script>
        $(document).ready(function () {

            $("body").on("click", ".delete_post", function () {
                var this_v = $(this);
                var this_id = this_v.data('id');


                swal({
                    title: "{{__('Order will be deleted permanently!')}}",
                    text: "{{__('Are you sure to proceed?')}}",
                    type: "{{__('warning')}}}",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "{{__('Yes, Remove Order!')}}",
                    cancelButtonText: "{{__('No, I am not sure!')}}",

                }).then(function (isConfirm) {


                    // alert(isConfirm);
                    if (isConfirm.value) {

                        $.ajax({
                            url: '{{route('admin.products.orders.index')}}' + '/' + this_id,
                            type: 'delete', // replaced from put
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (r) {
                                $(this_v).closest('.media-item').remove();

                            },
                            error: function (msg) {
                                $('.preloader').hide();
                                console.log(msg.responseJSON.message);
                                swal({
                                    title: "Error!",
                                    text: msg.responseJSON.message,
                                    type: "error",
                                    showCancelButton: false,
                                    confirmButtonColor: "#DD6B55",

                                })
                            }
                        });



                    }
                    else {

                    }

                });


            });




        });

    </script>
</div>