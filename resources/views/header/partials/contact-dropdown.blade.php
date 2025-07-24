
@php

$listaUnica = [];

if (!empty($config['phone'])) {
    foreach ($config['phone'] as $item) {
        $groupTitle = $item['title'] ?? '';
        unset($item['title']);
        $item['groupTitle'] = $groupTitle;
        $listaUnica[] = $item;
    }
}
@endphp
<div class="dropdown-menu dropdown-menu-end rounded-3 p-4 border shadow my-1 my-lg-2 animate slideIn" style="width:300px;">
  <div class="fs-5 ms-1 mb-3 text-dary fw-semibold d-flex justify-content-between">
    <div>Atendimento</div>
  </div>
  <div class="d-flex gap-3 flex-column">
    @foreach($listaUnica as $item)
      <div class="px-3 py-2 border rounded-3">
        <span class="fs-8"> {{ !empty($item['groupTitle']) ? $item['groupTitle'] : ucfirst($item['name'] ?? '') }}</span>
          <a href="{{ $item['name'] === 'whatsapp' ? 'https://wa.me/' : 'tel:' }}{{ preg_replace('/\D/', '', $item['phone']) }}" class="nav-link w-100 d-flex justify-content-between fs-5" target="_blank" rel="noopener noreferrer">{{ $item['phone'] }}<i class="{{ $item['name'] === 'whatsapp' ? 'icon-whatsapp text-whatsapp' : 'icon-phone text-primary' }} fs-4"></i></a>
      </div>
    @endforeach
    <a href="/institucional/fale-conosco" class="btn btn-lg btn-outline-secondary w-100 fs-6 rounded-3 py-3">Entre em fale conosco</a>
  </div>
</div>