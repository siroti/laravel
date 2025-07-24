document.addEventListener("DOMContentLoaded", function () {
    const minInputs = document.querySelectorAll("#minPrice, #minPriceRural, #minPriceAluguel");
    const maxInputs = document.querySelectorAll("#maxPrice, #maxPriceRural, #maxPriceAluguel");
    const aplicarButtons = document.querySelectorAll(".btnValue, .btnValueRural, .btnValueAluguel");
    const groupValueEls = document.querySelectorAll("#groupValue, #groupValueRural, #groupValueAluguel");
    const aplicar10s = document.querySelectorAll("#aplicar10, #aplicar10Rural, #aplicar10Aluguel");
    const inputValors = document.querySelectorAll("#valor, #valorRural, #valorAluguel");
    const limparValors = document.querySelectorAll(".limparValor, .limparValorRural, .limparValorAluguel");
    const sliders = document.querySelectorAll("#priceRange, #priceRangeRural, #priceRangeAluguel");


    const rangeValuesVenda = [
        { percent: 0, value: 0 }, { percent: 4, value: 10000 }, { percent: 8, value: 25000 }, { percent: 12, value: 50000 },
        { percent: 14, value: 100000 }, { percent: 16, value: 125000 }, { percent: 20, value: 150000 },
        { percent: 24, value: 200000 }, { percent: 28, value: 250000 }, { percent: 32, value: 300000 },
        { percent: 36, value: 350000 }, { percent: 40, value: 400000 }, { percent: 44, value: 450000 },
        { percent: 48, value: 500000 }, { percent: 50, value: 550000 }, { percent: 52, value: 600000 },
        { percent: 54, value: 650000 }, { percent: 58, value: 700000 }, { percent: 62, value: 750000 },
        { percent: 66, value: 800000 }, { percent: 70, value: 850000 }, { percent: 74, value: 900000 },
        { percent: 78, value: 950000 }, { percent: 82, value: 1000000 }, { percent: 86, value: 1500000 },
        { percent: 88, value: 2000000 }, { percent: 90, value: 3000000 }, { percent: 94, value: 4000000 },
        { percent: 98, value: 10000000 },{ percent: 100, value: 10000001 },
    ];

    const rangeValuesAluguel = [
        { percent: 0, value: 0 },{ percent: 10, value: 100 },{ percent: 20, value: 500 },{ percent: 30, value: 1000 },
        { percent: 40, value: 1500 },{ percent: 50, value: 2000 }, { percent: 55, value: 3000 },
        { percent: 60, value: 5000 }, { percent: 70, value: 7000 }, { percent: 80, value: 10000 },
        { percent: 90, value: 50000 }, { percent: 95, value: 100000 }, { percent: 100, value: 100001 }
    ];

    // NOVO: Range para Rural
    const rangeValuesRural = [
        { percent: 0, value: 0 },{ percent: 4, value: 10000 },{ percent: 8, value: 250000 },{ percent: 12, value: 500000 },
        { percent: 14, value: 750000 }, { percent: 16, value: 1000000 }, { percent: 20, value: 2000000 },
        { percent: 24, value: 3000000 }, { percent: 28, value: 4000000 }, { percent: 32, value: 5000000 },
        { percent: 36, value: 7500000 }, { percent: 40, value: 10000000 }, { percent: 44, value: 20000000 },
        { percent: 48, value: 30000000 }, { percent: 50, value: 40000000 }, { percent: 52, value: 50000000 },
        { percent: 54, value: 75000000 }, { percent: 58, value: 100000000 }, { percent: 62, value: 200000000 },
        { percent: 66, value: 300000000 }, { percent: 70, value: 400000000 }, { percent: 74, value: 500000000 },
        { percent: 78, value: 750000000 }, { percent: 80, value: 1000000000 }, { percent: 82, value: 2000000000 },
        { percent: 84, value: 3000000000 }, { percent: 88, value: 4000000000 }, { percent: 90, value: 5000000000 },
        { percent: 94,value: 7500000000 },{ percent: 98,value: 10000000000 },{ percent: 100,value: 10000000001 },
    ];


    function createRange(rangeValues) {
        return rangeValues.reduce((acc, item) => {
            acc[`${item.percent}%`] = item.value;
            return acc;
        }, { min: rangeValues[0].value, max: rangeValues[rangeValues.length - 1].value });
    }

    const rangeVenda = createRange(rangeValuesVenda);
    const rangeAluguel = createRange(rangeValuesAluguel);
    // NOVO:
const rangeRural = createRange(rangeValuesRural)

    sliders.forEach((slider, index) => {
      const minInput = minInputs[index];
      const maxInput = maxInputs[index];
      const aplicarButton = aplicarButtons[index];
      const groupValueEl = groupValueEls[index];
      const aplicar10 = aplicar10s[index];
      const inputValor = inputValors[index];
      const limparValor = limparValors[index];

      if (!minInput || !maxInput || !aplicarButton || !groupValueEl || !aplicar10 || !inputValor || !limparValor) {
          console.warn(`Elemento não encontrado para índice ${index}`);
          return; // pula essa iteração
      }

      addCurrencyEvents(minInput);
      addCurrencyEvents(maxInput);

      const dropdown = new bootstrap.Dropdown(groupValueEl);

      const isAluguel = slider.id.includes('Aluguel');
      const isRural = slider.id.includes('Rural');
      const range = isAluguel ? rangeAluguel : isRural ? rangeRural : rangeVenda;

      noUiSlider.create(slider, {
          start: [range.min, range.max],
          connect: true,
          snap: true,
          range,
          format: {
              to: value => "R$ " + new Intl.NumberFormat("pt-BR").format(value),
              from: value => Number(value.replace(/[^\d]/g, ""))
          }
      });

      const saved = JSON.parse(localStorage.getItem(`selected_${slider.id}`));
      if (saved) {
          minInput.value = saved.min ? formatCurrency(saved.min) : "";
          maxInput.value = saved.max ? formatCurrency(saved.max) : "";
          inputValor.value = saved.label || "Todos";
          inputValor.textContent = saved.label || "Todos";
          slider.noUiSlider.set([saved.min || range.min, saved.max || range.max]);
          aplicar10.checked = !!saved.aplicar10;
          toggleLimparValor(inputValor, limparValor);
      }

      slider.noUiSlider.on("update", function (values, handle) {
          const cleanValue = (str) => Number(str.replace(/[^\d]/g, ""));
          const valorMin = cleanValue(values[0]);
          const valorMax = cleanValue(values[1]);

          if (handle === 0) minInput.value = (valorMin <= range.min) ? "" : formatCurrency(valorMin);
          if (handle === 1) maxInput.value = (valorMax >= range.max) ? "" : formatCurrency(valorMax);
      });

      aplicarButton.addEventListener("click", function () {
          const minValue = Number(minInput.value.replace(/[^\d]/g, ""));
          const maxValue = Number(maxInput.value.replace(/[^\d]/g, ""));
          let result = minValue && maxValue ? `${formatCurrency(minValue)} à ${formatCurrency(maxValue)}` :
                      minValue ? `À partir de ${formatCurrency(minValue)}` :
                      maxValue ? `Até ${formatCurrency(maxValue)}` : "Todos";

          inputValor.value = result;
          inputValor.textContent = result;

          dropdown.hide();
          toggleLimparValor(inputValor, limparValor);

          localStorage.setItem(`selected_${slider.id}`, JSON.stringify({
              min: minValue || null,
              max: maxValue || null,
              label: result,
              aplicar10: aplicar10.checked
          }));
      });

      limparValor.addEventListener('click', () => {
          inputValor.value = "Todos";
          minInput.value = '';
          maxInput.value = '';
          aplicar10.checked = false;
          slider.noUiSlider.set([range.min, range.max]);
          toggleLimparValor(inputValor, limparValor);

          localStorage.removeItem(`selected_${slider.id}`);
      });

      toggleLimparValor(inputValor, limparValor);
    });

});

// ✅ Utilitários
function formatNumber(value) {
    return new Intl.NumberFormat("pt-BR").format(value);
}

function formatCurrency(value) {
    return "R$ " + new Intl.NumberFormat("pt-BR").format(value);
}

function handleInput(event) {
    let rawValue = event.target.value.replace(/[^\d]/g, "");
    if (rawValue) event.target.value = formatCurrency(rawValue);
}

function handleFocus(event) {
    event.target.value = event.target.value.replace(/^R\$ ?/, "");
}

function handleBlur(event) {
    handleInput(event);
}

function addCurrencyEvents(input) {
    input.addEventListener("input", handleInput);
    input.addEventListener("focus", handleFocus);
    input.addEventListener("blur", handleBlur);
}

function toggleLimparValor(inputValor, limparValor) {
    limparValor.classList.toggle('d-none', inputValor.value === "Todos");
}

// Evento para os rádios do tipo de negócio
document.querySelectorAll('input[name="b_negocio"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
        // IDs dos campos de valor e sliders
        const campos = [
            { slider: 'priceRange', min: 'minPrice', max: 'maxPrice', input: 'valor', aplicar10: 'aplicar10', limpar: 'limparValor' },
            { slider: 'priceRangeRural', min: 'minPriceRural', max: 'maxPriceRural', input: 'valorRural', aplicar10: 'aplicar10Rural', limpar: 'limparValorRural' },
            { slider: 'priceRangeAluguel', min: 'minPriceAluguel', max: 'maxPriceAluguel', input: 'valorAluguel', aplicar10: 'aplicar10Aluguel', limpar: 'limparValorAluguel' }
        ];

        campos.forEach(function(campo) {
            // Limpa os campos de input
            const minInput = document.getElementById(campo.min);
            const maxInput = document.getElementById(campo.max);
            const inputValor = document.getElementById(campo.input);
            const aplicar10 = document.getElementById(campo.aplicar10);
            const slider = document.getElementById(campo.slider);
            const limparValor = document.querySelector(`.${campo.limpar}`);

            if (minInput) minInput.value = '';
            if (maxInput) maxInput.value = '';
            if (inputValor) {
                inputValor.value = "Todos";
                inputValor.textContent = "Todos";
            }
            if (aplicar10) aplicar10.checked = false;
            if (slider && slider.noUiSlider) {
                // Reseta o slider para o range inicial
                let range = slider.noUiSlider.options.range;
                slider.noUiSlider.set([range.min, range.max]);
            }
            if (limparValor) limparValor.classList.add('d-none');

            // Limpa o localStorage correspondente
            localStorage.removeItem(`selected_${campo.slider}`);
        });
    });
});