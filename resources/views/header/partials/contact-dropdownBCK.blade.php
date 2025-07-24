
@php
    $uid = uniqid('contato_');
    $agrupado = [];

    if (!empty($config['phone'])) {
        foreach ($config['phone'] as $item) {
            $title = $item['title'];
            unset($item['title']);
            $agrupado[$title][] = $item;
        }
    }
@endphp

<script>
  window.contatos = @json($agrupado);
</script>

<div class="dropdown-menu dropdown-menu-end rounded-3 p-4 border shadow my-1 my-lg-2 animate slideIn" data-uid="{{ $uid }}" style="width:300px;">
  <div class="fs-5 ms-1 mb-3 text-dary fw-semibold d-flex justify-content-between">
    <div>Contato</div>
    <div>
      <div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="{{ $uid }}" id="{{ $uid }}_1" autocomplete="off" checked>
        <label class="btn btn-outline-secondary fs-8" for="{{ $uid }}_1">Telefone</label>
      
        <input type="radio" class="btn-check" name="{{ $uid }}" id="{{ $uid }}_2" autocomplete="off">
        <label class="btn btn-outline-secondary fs-8" for="{{ $uid }}_2">E-mail</label>
      </div>
    </div> 
  </div>	

  <div class="mb-3">
    <div class="form-floating col mb-3">
      <select class="form-select" id="{{ $uid }}_select1" aria-label="Unidade">
      </select>
      <label for="{{ $uid }}_select1">Unidade</label>
    </div>
  </div>

  <div class="form-floating col mb-3">
    <select class="form-select" id="{{ $uid }}_select2" aria-label="Telefone">
    </select>
    <label for="{{ $uid }}_select2">Telefone</label>
  </div>

  <div class="d-grid gap-2 d-md-flex">
    <button class="col btn btn-outline-secondary me-md-1 fs-7 btn-lg rounded" type="button">Ligar</button>
    <button id="{{ $uid }}_whatsappBtn" class="col btn btn-secondary fs-7 btn-lg rounded" type="button">WhatsApp</button>
  </div>
</div>

@push('js-push')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const uid = "{{ $uid }}";
  const contatosData = window.contatos || {};
  const unidadeSelect = document.getElementById(`${uid}_select1`);
  const telefoneSelect = document.getElementById(`${uid}_select2`);
  const whatsappBtn = document.getElementById(`${uid}_whatsappBtn`);

  // Preencher unidades
  for (const unidade in contatosData) {
    const opt = document.createElement('option');
    opt.value = unidade;
    opt.textContent = unidade;
    unidadeSelect.appendChild(opt);
  }

  function updateTelefones() {
    const unidade = unidadeSelect.value;
    const telefones = contatosData[unidade] || [];
    const savedPhone = localStorage.getItem(`telefone_${unidade}`);
    let selectedPhone = null;

    telefoneSelect.innerHTML = '';

    telefones.forEach((contato, index) => {
      const option = document.createElement('option');
      option.value = contato.phone;
      option.textContent = contato.phone;
      option.dataset.name = contato.name || '';

      if (savedPhone && contato.phone === savedPhone) {
        option.selected = true;
        selectedPhone = contato.phone;
      }

      telefoneSelect.appendChild(option);
    });

    if (!selectedPhone && telefones.length > 0) {
      telefoneSelect.options[0].selected = true;
      selectedPhone = telefones[0].phone;
    }

    if (selectedPhone) {
      localStorage.setItem(`telefone_${unidade}`, selectedPhone);
    }

    updateWhatsAppButton();
  }

  function updateWhatsAppButton() {
    const selected = telefoneSelect.options[telefoneSelect.selectedIndex];
    whatsappBtn.disabled = !(selected?.dataset?.name === 'whatsapp');
  }

  unidadeSelect.addEventListener('change', () => {
    const unidade = unidadeSelect.value;
    localStorage.setItem('global_selected_unidade', unidade);
    updateTelefones();
  });

  telefoneSelect.addEventListener('change', () => {
    const telefone = telefoneSelect.value;
    const unidade = unidadeSelect.value;
    localStorage.setItem(`telefone_${unidade}`, telefone);
    updateWhatsAppButton();
  });

  // Inicialização ao abrir
  const savedUnidade = localStorage.getItem('global_selected_unidade');
  if (savedUnidade && contatosData[savedUnidade]) {
    unidadeSelect.value = savedUnidade;
  }

  updateTelefones(); // atualiza telefone com base na unidade (inclusive salva telefone se houver)
});
// Sempre que o dropdown for aberto, atualiza os selects
document.querySelectorAll('.dropdown-menu[data-uid]').forEach(dropdown => {
  dropdown.addEventListener('shown.bs.dropdown', function () {
    const uid = dropdown.dataset.uid;

    // Só atualiza se for o componente atual
    if (uid === "{{ $uid }}") {
      const savedUnidade = localStorage.getItem('global_selected_unidade');
      if (savedUnidade && contatosData[savedUnidade]) {
        unidadeSelect.value = savedUnidade;
      }
      updateTelefones();
    }
  });
});

</script>
@endpush
