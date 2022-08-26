<div class="modal-header pb-2 pt-2">
    <h5 class="modal-title" id="exampleModalLongTitle">{{ $tracker->project_task}} <small>( {{$tracker->total}}, {{date('d M',strtotime($tracker->start_time))}} )</small></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body p-1">
      <div class="row ">
        <div class="col-lg-12 product-left mb-5 mb-lg-0">
            @if( $images->count() > 0)
            <div class="swiper-container product-slider mb-2 pb-2" style="border-bottom:solid 2px #f2f3f5">
                <div class="swiper-wrapper">
                    @foreach ($images as $image)
                        <div class="swiper-slide" id="slide-{{$image->id}}">
                            <img src="{{ asset(Storage::url($image->img_path))}}" alt="..."  class="img-fluid">
                            <div class="time_in_slider">{{date('H:i:s, d M ',strtotime($image->time))}} | 
                                <a href="#" class="delete-icon"  data-confirm-delete="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="removeImage({{$image->id}})">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach            
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
            
            <div class="swiper-container product-thumbs">
                <div class="swiper-wrapper">
                    @foreach ($images as $image)
                    <div class="swiper-slide" id="slide-thum-{{$image->id}}">
                        <img src="{{ asset(Storage::url($image->img_path))}}" alt="..." class="img-fluid">
                    </div>
                    @endforeach 
                
                </div>
            </div>
            @else
            <div class="no-image">
                <h5 class="text-muted">Images Not Available .</h5>
            </div>
            @endif
        </div>
      </div>
  </div>
  <script type="text/javascript">
    $('[data-confirm]').each(function () {
    var me = $(this),
        me_data = me.data('confirm-delete');
        me_data = me_data.split('-');
    me.fireModal({
        title: me_data[0],
        body: me_data[1],
        buttons: [
            {
                text: me.data('confirm-text-yes') || 'Yes',
                class: 'btn btn-sm btn-danger rounded-pill',
                handler: function (modal) {
                    eval(me.data('confirm-yes'));
                    $.destroyModal(modal);
                }
            },
            {
                text: me.data('confirm-text-cancel') || 'Cancel',
                class: 'btn btn-sm btn-secondary rounded-pill',
                handler: function (modal) {
                    $.destroyModal(modal);
                    eval(me.data('confirm-no'));
                }
            }
        ]
    })
});
</script>