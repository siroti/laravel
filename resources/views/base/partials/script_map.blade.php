@push('js-top-push')
  @if(isset($config['general']['site_maps_google']))
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&language=pt-BR&key={{$config['general']['site_maps_google']}}" async></script>
  @else
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&language=pt-BR&key=AIzaSyDFuG_XMrpatJmvYbq5MdtKO9cBU3-ZW0U" async></script>
  @endif  
@endpush