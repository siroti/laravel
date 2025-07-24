const { values } = require("lodash");
const controller = new AbortController();
const timeoutId = setTimeout(() => controller.abort(), 5000); // 5s timeout

document.addEventListener('DOMContentLoaded', function () { 

        const buscaBairro = document.querySelector('#buscaBairro');
        const bairrosContainer = document.querySelector('#neighborhood');
        const bairros = [];
        const marcador = document.querySelector('#marcador');
        const marcarTodos = document.querySelector('#marcartodos');
        const limparBtn = document.querySelector('.limpar-bairro');
        const limparBairro = document.querySelector('.limparBairro');
        const aplicarBtn = document.querySelector('.btn-aplicar');
        const bairrosSelecionado = document.querySelector('#bairrosSelecionados');
        const fecharModalBtn = document.querySelector('.close-bairro');
        const valueBairro = document.querySelector('.valueBairro'); 
        const limparSubtipo = document.querySelector(".limparSubtipo"); 
        const subtipoInput = document.querySelector('#b_subtipo');
        const selectBairro = document.querySelector(".select-bairro");

        

        const modalEl = document.getElementById('groupBairro'); // o ID do seu modal

        if (modalEl && buscaBairro) {
            modalEl.addEventListener('shown.bs.modal', function () {
                buscaBairro.removeAttribute('readonly'); // garante que está editável
                buscaBairro.focus(); // foco com cursor piscando
            });
        }

        const selecionados = [...document.querySelectorAll('.bairro-checkbox:checked')];
        valueBairro.value = selecionados.length > 0 ? `${selecionados.length} selecionado${selecionados.length === 1 ? '' : 's'}` : 'Todos';


        // Função validação
        function isInvalid() {
            const selectContainers = document.querySelectorAll(".select-container");
            selectContainers.forEach(selectContainer => {
                const input = selectContainer.querySelector("input");
                
                input.addEventListener("input", function () {
                    if (input.value.trim()) {
                        selectContainer.classList.remove("is-invalid");
                    } 
                });
                input.addEventListener("blur", function () {
                    setTimeout(() => {
                        const selectedRadio = document.querySelector('[name="tipo"]:checked');
                        if (input.value.trim()) {
                            selectContainer.classList.remove("is-invalid");
                        } else if(selectedRadio) {
                            selectContainer.classList.remove("is-invalid");
                        }
                    }, 500);
                });
            });
        }
        // Final Função validação

        // sem Cache para reset a cada chamada do negócio
        /* async function carregarTipo() {
            let timeoutCarregarTipo;
            let cacheTipos = {}; 
            const SelectTipo = document.querySelector('#select-tipo');
            clearTimeout(timeoutCarregarTipo); 

            timeoutCarregarTipo = setTimeout(async () => {
                isInvalid();
                try {
                    const cityId = document.querySelector('#padraoCidade')?.value || document.querySelector('[name="local"]:checked')?.value;
                    const business = document.querySelector('[name="b_negocio"]:checked')?.value;
                    console.log("teste");
                    SelectTipo.setAttribute("disabled", "disabled");

                    if (!cityId) {
                        console.error("Nenhuma cidade selecionada.");
                        return;
                    }
                    const cacheKey = `${cityId}_${business}`;

                    // ⚡ Evita requisição desnecessária se já temos os dados no cache
                    if (cacheTipos[cacheKey]) {
                        atualizarTipos(cacheTipos[cacheKey]);
                        setTimeout(async () => {SelectTipo.removeAttribute("disabled");}, 500);
                        return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    // Realizando a requisição usando fetch
                    const response = await fetch(`/pesquisa/tipo`, {
                        method: 'POST',
                        body: JSON.stringify({
                            cityId: cityId,
                            business: business
                        }),
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        credentials: 'include'
                    });

                    if (!response.ok) {
                        throw new Error(`Erro HTTP! Status: ${response.status}`);
                    }

                    const data = await response.json();
                    //console.log("Dados recebidos da API:", data);
                    // ⚡ Salva no cache

                    cacheTipos[cacheKey] = data;
                    // Atualiza os tipos
                    atualizarTipos(data);
                    setTimeout(async () => {SelectTipo.removeAttribute("disabled");}, 500);

                } catch (error) {
                    if (error.message === "Failed to fetch") {
                        console.error("Erro de conexão: O servidor pode estar indisponível.");
                    } else {
                        console.error("Erro ao buscar tipos:", error);
                    }
                }
            }, 100);
        }*/

        // Cache preservado fora da função para evitar reset a cada chamada
        const cacheTipos = {};
        let timeoutCarregarTipo;

        async function carregarTipo() {
            clearTimeout(timeoutCarregarTipo);
            const SelectTipo = document.querySelector('#select-tipo');

            timeoutCarregarTipo = setTimeout(async () => {
                isInvalid();
                try {
                    const cityId = document.querySelector('#padraoCidade')?.value || document.querySelector('[name="local"]:checked')?.value;
                    const business = document.querySelector('[name="b_negocio"]:checked')?.value;

                    SelectTipo.setAttribute("disabled", "disabled");

                    if (!cityId) {
                        console.error("Nenhuma cidade selecionada.");
                        SelectTipo.removeAttribute("disabled");
                        return;
                    }

                    const cacheKey = `${cityId}_${business}`;

                    // Se já tem no cache, usa e sai
                    if (cacheTipos[cacheKey]) {
                        atualizarTipos(cacheTipos[cacheKey]);
                        SelectTipo.removeAttribute("disabled");
                        return;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                    const response = await fetch(`/pesquisa/tipo`, {
                        method: 'POST',
                        body: JSON.stringify({ cityId, business }),
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        credentials: 'include',
                        //signal: controller.signal 
                    });
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        throw new Error(`Erro HTTP! Status: ${response.status}`);
                    }
                    const data = await response.json();
                    cacheTipos[cacheKey] = data;
                    atualizarTipos(data);
                    SelectTipo.removeAttribute("disabled");

                } catch (error) {
                    console.error("Erro ao buscar tipos:", error);
                    SelectTipo.removeAttribute("disabled");
                }
            }, 900);
        }

        // Configura listener para rodar quando clicar em qualquer input b_negocio
        document.querySelectorAll('[name="b_negocio"]').forEach(radio => {
            radio.addEventListener('change', carregarTipo);
        });
    

        /*function atualizarTipos(data) {
            const tiposContainer = document.querySelector('#tipo-container');
            const subtiposContainer = document.querySelector('#subtipo-container');
            subtiposContainer.innerHTML = '';
            tiposContainer.innerHTML = '';

            let tipoIndex = 1;

            if (data && typeof data === 'object') {
                const savedTipo = localStorage.getItem('selected_tipo');

                Object.keys(data).forEach((category) => {
                    const firstType = data[category][0];

                    if (firstType) {
                        const input = document.createElement('input');
                        input.type = 'radio';
                        input.className = 'btn-check';
                        input.name = 'tipo';
                        input.id = `tipo${tipoIndex}`;
                        input.value = firstType;
                        input.autocomplete = 'off';

                        if (savedTipo === firstType) {
                            input.checked = true;
                            // Tenta carregar subtipo, com tentativas se data[category] estiver vazio
                            let tentativas = 0;
                            const maxTentativas = 10;
                            function tentarCarregarSubtipo() {
                                if (Array.isArray(data[category]) && data[category].length > 0) {
                                    carregarSubtipo(data[category]);
                                } else if (tentativas < maxTentativas) {
                                    tentativas++;
                                    setTimeout(tentarCarregarSubtipo, 150);
                                }
                            }
                            tentarCarregarSubtipo();
                            document.querySelector('#b_tipo')?.classList.remove('d-none');

                            setTimeout(() => {
                                const bTipo = document.querySelector('#b_tipo');
                                if(bTipo) bTipo.value = savedTipo;
                                //console.log("Tipo da sessão:", savedTipo);
                                //Adcionar abaxio senão deixar marcado
                                const radios = document.querySelectorAll('input[name="subtipo"]');
                                const inputSubtipo = document.getElementById("b_subtipo");
                                radios.forEach(function (radio) {
                                    radio.addEventListener("change", function () {
                                        if (this.checked) {
                                            inputSubtipo.value = this.value;
                                            inputSubtipo.classList.remove("text-muted");
                                        }
                                    });
                                });
                            }, 600);
                        }

                        input.addEventListener('change', () => {
                            localStorage.setItem('selected_tipo', input.value);
                            carregarSubtipo(data[category]);
                            document.querySelector('#b_tipo')?.classList.remove('d-none');
                        });

                        const label = document.createElement('label');
                        label.className = 'btn btn-bg-secondary text-start';
                        label.htmlFor = input.id;
                        label.innerHTML = firstType;

                        tiposContainer.appendChild(input);
                        tiposContainer.appendChild(label);

                        tipoIndex++;
                    }
                });
            } else {
                console.log('Nenhum tipo encontrado');
            }
        }*/

            function atualizarTipos(data) {
    const tiposContainer = document.querySelector('#tipo-container');
    const subtiposContainer = document.querySelector('#subtipo-container');
    subtiposContainer.innerHTML = '';
    tiposContainer.innerHTML = '';

    let tipoIndex = 1;

    if (data && typeof data === 'object') {
        const savedTipo = localStorage.getItem('selected_tipo');

        Object.keys(data).forEach((category) => {
            const firstType = data[category][0];

            if (firstType) {
                const input = document.createElement('input');
                input.type = 'radio';
                input.className = 'btn-check';
                input.name = 'tipo';
                input.id = `tipo${tipoIndex}`;
                input.value = firstType;
                input.autocomplete = 'off';

                if (savedTipo === firstType) {
                    input.checked = true;
                    // Chama carregar subtipo...
                    let tentativas = 0;
                    const maxTentativas = 10;
                    function tentarCarregarSubtipo() {
                        if (Array.isArray(data[category]) && data[category].length > 0) {
                            carregarSubtipo(data[category]);
                        } else if (tentativas < maxTentativas) {
                            tentativas++;
                            setTimeout(tentarCarregarSubtipo, 150);
                        }
                    }
                    tentarCarregarSubtipo();
                    document.querySelector('#b_tipo')?.classList.remove('d-none');

                    // Atualiza o campo b_tipo imediatamente
                    setTimeout(() => {
                        const bTipo = document.querySelector('#b_tipo');
                        if(bTipo) {
                            bTipo.value = savedTipo;
                            console.log("Tipo restaurado do localStorage:", savedTipo);
                        }
                        
                        // Configure listeners para subtipos
                        const radios = document.querySelectorAll('input[name="subtipo"]');
                        const inputSubtipo = document.getElementById("b_subtipo");
                        radios.forEach(function (radio) {
                            radio.addEventListener("change", function () {
                                if (this.checked) {
                                    inputSubtipo.value = this.value;
                                    inputSubtipo.classList.remove("text-muted");
                                }
                            });
                        });
                    }, 100); // Reduzido de 600ms para 100ms
                }

                // Listener para mudanças
                input.addEventListener('change', () => {
                    localStorage.setItem('selected_tipo', input.value);
                    carregarSubtipo(data[category]);
                    document.querySelector('#b_tipo')?.classList.remove('d-none');
                    
                    // Atualiza b_tipo imediatamente
                    const bTipo = document.querySelector('#b_tipo');
                    if(bTipo) {
                        bTipo.value = input.value;
                        console.log("Tipo atualizado:", input.value);
                    }
                });

                const label = document.createElement('label');
                label.className = 'btn btn-bg-secondary text-start';
                label.htmlFor = input.id;
                label.innerHTML = firstType;

                tiposContainer.appendChild(input);
                tiposContainer.appendChild(label);

                tipoIndex++;
            }
        });
        
        // Chama selectTipo após criar todos os elementos
        setTimeout(() => {
            selectTipo();
        }, 100);
    } else {
        console.log('Nenhum tipo encontrado');
    }
}

        let timeoutCarregarSubtipo;
        async function carregarSubtipo(subtiposPromise) {
            clearTimeout(timeoutCarregarSubtipo);
            const SelectSubtipo2 = document.querySelector('#select-subtipo');
            if(SelectSubtipo2){
                SelectSubtipo2.removeAttribute("disabled");
                SelectSubtipo2.classList.remove("bg-body-secondary");
                SelectSubtipo2.classList.add("bg-body");
            }
            timeoutCarregarSubtipo = setTimeout(async () => {
                const subtiposContainer = document.querySelector('#subtipo-container');
                const SelectSubtipo = document.querySelector('#select-subtipo');
                subtiposContainer.innerHTML = ''; 
            
                try {
                    let subtipos = await Promise.resolve(subtiposPromise); // Aguarda os subtipos se forem uma Promise
            
                    if (Array.isArray(subtipos)) {
                        subtipos = subtipos.slice(1); // Remove o primeiro item do array
                    }

                    // Recupera subtipo salvo
                    const savedSubtipo = localStorage.getItem('selected_subtipo');
            
                    subtipos.forEach((subtipo, index) => {
                        const input = document.createElement('input');
                        input.type = 'radio';
                        input.className = 'btn-check';
                        input.name = 'subtipo';
                        input.id = `subtipo${index + 1}`;
                        input.value = subtipo;
                        input.autocomplete = 'off';

                        // Marca o subtipo salvo
                        if (savedSubtipo === subtipo) {
                            input.checked = true;
                            setTimeout(() => {
                                const bSubtipo = document.getElementById("b_subtipo");
                                if (bSubtipo) bSubtipo.value = savedSubtipo;
                            }, 100);
                        }
                
                        input.addEventListener('change', () => {
                            localStorage.setItem('selected_subtipo', input.value);
                        });
                
                        const label = document.createElement('label');
                        label.className = 'btn btn-bg-secondary text-start';
                        label.htmlFor = input.id;
                        label.innerHTML = subtipo;
                
                        subtiposContainer.appendChild(input);
                        subtiposContainer.appendChild(label);
                    });
                    SelectSubtipo.removeAttribute("disabled");
                    SelectSubtipo.classList.remove("bg-body-secondary");
                    SelectSubtipo.classList.add("bg-body");
                } catch (error) {
                    console.error('Erro ao carregar subtipos:', error);
                }
            }, 300);
        }

        /*function AtualizarSelectTipo(){
                const tipoInput = document.getElementById("b_tipo");
                const SelectSubtipo = document.querySelector('#select-subtipo');

                // Se algum tipo estiver marcado, atualiza o valor, senão deixa vazio
                const tipoSelecionado = document.querySelector('input[name="tipo"]:checked');
                if (tipoSelecionado) {
                    tipoInput.value = tipoSelecionado.value;
                } else {
                    tipoInput.value = '';
                }
                console.log("Atualizando Select Tipo:", tipoInput.value);
                //SelectSubtipo.setAttribute("disabled", "disabled");
               // SelectSubtipo.classList.add("bg-body-secondary");
               // SelectSubtipo.classList.remove("bg-body");
                subtipoInput.value = 'Todos';
                
                limparSutipoHandler();
                selectTipo();
        }*/
       function AtualizarSelectTipo(){
    const tipoInput = document.getElementById("b_tipo");
    const SelectSubtipo = document.querySelector('#select-subtipo');

    // Se algum tipo estiver marcado, atualiza o valor, senão deixa vazio
    const tipoSelecionado = document.querySelector('input[name="tipo"]:checked');
    if (tipoSelecionado && tipoSelecionado.nextElementSibling) {
        const labelText = tipoSelecionado.nextElementSibling.textContent.trim();
        tipoInput.value = labelText;
        console.log("AtualizarSelectTipo - Tipo encontrado:", labelText);
    } else {
        tipoInput.value = '';
        console.log("AtualizarSelectTipo - Nenhum tipo selecionado");
    }
    
    subtipoInput.value = 'Todos';
    limparSutipoHandler();
    
    // Chama selectTipo para garantir que os listeners estão ativos
    setTimeout(() => {
        selectTipo();
    }, 50);
}

         let timeoutCarregarBairros; 
        let cacheBairros = {};
        async function carregarBairros() {
            clearTimeout(timeoutCarregarBairros);
            timeoutCarregarBairros = setTimeout(async () => {
                const SelectSubtipo = document.querySelector('#select-subtipo');
                const modal = document.querySelector('#groupBairro');    
                try {
                    const estados = [
                        'acre', 'alagoas', 'amapa', 'amazonas', 'bahia', 'ceara', 'distrito-federal', 'espirito-santo', 'goias',
                        'maranhao', 'mato-grosso', 'mato-grosso-do-sul', 'minas-gerais', 'para', 'paraiba', 'parana',
                        'pernambuco', 'piaui', 'rio-de-janeiro', 'rio-grande-do-norte', 'rio-grande-do-sul',
                        'rondonia', 'roraima', 'santa-catarina', 'sao-paulo', 'sergipe', 'tocantins'
                    ];

                    let cityId = document.querySelector('[name="local"]:checked')?.value;
                    const limparBairro = document.querySelector(".limparBairro");

                   // console.log("CityId selecionado:", cityId);
                   // console.log("Está entre os estados?", estados.includes(cityId));

                    
                    if (typeof cityId === "undefined") {
                        cityId = "Todos";
                    }

                    if (!cityId) {
                        console.log("CityId erro", cityId);
                        return;
                    } else if (cityId === "brasil" || estados.includes(cityId) || cityId === "Todos") {
                        AtualizarSelectTipo();
                        selectBairro.removeAttribute("data-bs-toggle");
                        selectBairro.removeAttribute("data-bs-target");
                        selectBairro.setAttribute("disabled", "disabled");
                        selectBairro.classList.add("bg-body-secondary");
                        document.querySelector('.valueBairro').value = 'Todos';
                        document.querySelector('#bairrosSelecionados').innerHTML = '';
                        if (cityId !== "Todos"){
                            limparLocalIcon.classList.remove('d-none');
                        }
                        if (limparBairro) {
                            limparBairro.remove();
                        }
                        setTimeout(() => {
                            try {
                                const neighborhood = document.querySelector('#neighborhood');
                                if (neighborhood) {
                                    neighborhood.querySelectorAll('[data-rel]').forEach(el => {
                                        if (el?.remove) el.remove();
                                    });
                                }
                            } catch (e) {
                                console.error("Erro ao remover bairros:", e);
                            }
                        }, 100);
                        bairros.length = 0;
                        modal.classList.add('disabled-modal');
                        //console.log("Nenhuma cidade selecionada.");
                        return;
                    } else {
                        //console.log("CityId:", cityId);
                        selectBairro.setAttribute("data-bs-toggle", "modal");
                        selectBairro.setAttribute("data-bs-target", "#groupBairro");
                        selectBairro.removeAttribute("disabled");
                        selectBairro.classList.remove("bg-body-secondary");
                        modal.classList.remove('disabled-modal');
                        limparLocalIcon.classList.remove('d-none');
                    }

                    // ⚡ Evita requisição desnecessária se já temos os dados no cache
                    if (cacheBairros[cityId]) {
                        //console.log("Usando cache para a cidade:", cityId);
                        return atualizarBairros(cacheBairros[cityId]); 
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    
                    const response = await fetch(`/pesquisa/bairro`, {
                        method: 'POST',
                        body: JSON.stringify({ cityId }),
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        credentials: 'include',
                        //signal: controller.signal 
                    });
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        throw new Error(`Erro HTTP! Status: ${response.status}`);
                    }

                    const data = await response.json();

                    // ⚡ Salva no cache
                    cacheBairros[cityId] = data;

                    atualizarBairros(data);
                } catch (error) {
                    console.error("Erro ao buscar bairros:", error);
                }
            }, 600);
        }


        function atualizarBairros(data) {
            const bairrosContainer = document.querySelector('#neighborhood');
            const bairrosSelecionado = document.querySelector('#bairrosSelecionados');
            const valueBairro = document.querySelector('.valueBairro'); 
            //const tipoInput = document.getElementById("b_tipo");
            //const SelectSubtipo = document.querySelector('#select-subtipo');
            //const cityPadrão = document.querySelector('#padraoCidade')?.value;

            // Limpa lista antes de atualizar
            bairrosContainer.innerHTML = ''; 
            bairrosSelecionado.innerHTML = '';
            //tipoInput.value = '';
            bairros.length = 0;  
            valueBairro.value = 'Todos';
            limparBairro.classList.toggle('d-none', valueBairro.value === "Todos");

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(bairro => {
                    const divCol = document.createElement('div');
                    divCol.setAttribute('data-rel', bairro.Nome);
                    divCol.className = 'col-12 col-md-6 my-1 my-md-0';

                    const divCheck = document.createElement('div');
                    divCheck.className = 'form-check mb-0';

                    const input = document.createElement('input');
                    input.className = 'form-check-input bairro-checkbox';
                    input.name = 'b_bairro[]';
                    input.type = 'checkbox';
                    input.id = `bairro-${bairro.Codigo}`;
                    input.value = bairro.Codigo;
                    input.title = bairro.Nome;

                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.htmlFor = input.id;
                    label.dataset.originalText = bairro.Nome;
                    label.innerHTML = `<strong></strong>${bairro.Nome}`;

                    divCheck.appendChild(input);
                    divCheck.appendChild(label);
                    divCol.appendChild(divCheck);
                    bairrosContainer.appendChild(divCol);

                    bairros.push({ id: bairro.Codigo, label, input });
                });

                //SelectSubtipo.setAttribute("disabled", "disabled");
                //SelectSubtipo.classList.add("bg-body-secondary");
                //SelectSubtipo.classList.remove("bg-body");
                
                AtualizarSelectTipo();

            } else {
                const divError = document.createElement('div');
                divError.className = 'col-12';
                divError.textContent = 'Nenhum bairro encontrado';
                bairrosContainer.appendChild(divError);
            }
        }

        function resetarFiltros() {
            carregarBairros();
            carregarTipo();
            //limparSubtipo.click();
        }


        const radioName = 'b_negocio';
        const savedNegocio = localStorage.getItem('selected_negocio');
        const negocioRadios = document.querySelectorAll(`input[name="${radioName}"]`);

        if (savedNegocio) {
            const savedRadio = [...negocioRadios].find(radio => radio.value === savedNegocio);
            if (savedRadio) savedRadio.checked = true;
        } else {
            // Se não tiver valor salvo, marca o primeiro
            if (negocioRadios.length > 0) {
                negocioRadios[0].checked = true;
            }
        }

        // Evento para os rádios
        negocioRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                localStorage.setItem('selected_negocio', this.value);
                resetarFiltros();
                limparDormitoriosDoStorage();
                limparDormitorio.classList.add('d-none');
            });
        });
        
         // Evento para o select de cidade
        document.getElementById('city').addEventListener('change', function () {
            resetarFiltros();
        });
        carregarBairros();
        carregarTipo();


        /*let timeoutSelectTipo; 
        async function selectTipo() {
            clearTimeout(timeoutSelectTipo); // Limpa qualquer timeout pendente
            
            const SelectSubtipo = document.querySelector('#select-subtipo');
            timeoutSelectTipo = setTimeout(async () => {
                document.querySelectorAll('input[name="tipo"]').forEach(radio => {
                    // Atualiza b_tipo se já estiver marcado ao carregar
                    if (radio.checked) {
                    const bTipoInput = document.getElementById("b_tipo");
                    if (bTipoInput && radio.nextElementSibling) {
                        bTipoInput.value = radio.nextElementSibling.textContent.trim();
                        bTipoInput.classList.remove("d-none");
                    }
                    // Chama selectSubtipo ao carregar se já estiver marcado
                    selectSubtipo();
                    }
                    radio.addEventListener('change', function () {
                        if (this.checked) {
                            const bTipoInput = document.getElementById("b_tipo");
                            if (bTipoInput && this.nextElementSibling) {
                            bTipoInput.value = this.nextElementSibling.textContent.trim();
                            bTipoInput.classList.remove("d-none");
                            }
                        }
                        selectSubtipo();
                    });
                    // Se nenhum tipo estiver marcado, desabilita o subtipo e limpa o valor
                    if (!document.querySelector('input[name="tipo"]:checked')) {
                        SelectSubtipo.setAttribute("disabled", "disabled");
                        SelectSubtipo.classList.add("bg-body-secondary");
                        SelectSubtipo.classList.remove("bg-body");
                        subtipoInput.value = 'Todos';
                       limparSutipoHandler();
                    }
                    
                });

                }, 150);
                
            localStorage.setItem('precisaAtualizar', 'false');
        }
            */
           let timeoutSelectTipo; 
        async function selectTipo() {
            clearTimeout(timeoutSelectTipo);
            
            timeoutSelectTipo = setTimeout(async () => {
                const tipoInputs = document.querySelectorAll('input[name="tipo"]');
                const bTipoInput = document.getElementById("b_tipo");
                const SelectSubtipo = document.querySelector('#select-subtipo');
                
                // Verifica se os elementos existem
                if (!bTipoInput || tipoInputs.length === 0) {
                    console.log("Elementos de tipo não encontrados ainda");
                    return;
                }

                tipoInputs.forEach(radio => {
                    // Remove listeners antigos para evitar duplicação
                    radio.removeEventListener('change', handleTipoChange);
                    
                    // Atualiza b_tipo se já estiver marcado ao carregar
                    if (radio.checked) {
                        const labelText = radio.nextElementSibling ? radio.nextElementSibling.textContent.trim() : '';
                        if (bTipoInput && labelText) {
                            bTipoInput.value = labelText;
                            bTipoInput.classList.remove("d-none");
                            console.log("Tipo atualizado:", labelText);
                        }
                        // Chama selectSubtipo ao carregar se já estiver marcado
                        selectSubtipo();
                    }
                    
                    // Adiciona o listener
                    radio.addEventListener('change', handleTipoChange);
                });

                // Se nenhum tipo estiver marcado, desabilita o subtipo
                const tipoSelecionado = document.querySelector('input[name="tipo"]:checked');
                if (!tipoSelecionado && SelectSubtipo) {
                    SelectSubtipo.setAttribute("disabled", "disabled");
                    SelectSubtipo.classList.add("bg-body-secondary");
                    SelectSubtipo.classList.remove("bg-body");
                    subtipoInput.value = 'Todos';
                    limparSutipoHandler();
                }
                
                localStorage.setItem('precisaAtualizar', 'false');
            }, 50); // Reduzido de 150ms para 50ms
        }
        // Função separada para o evento change
        function handleTipoChange() {
            const bTipoInput = document.getElementById("b_tipo");
            
            if (this.checked && bTipoInput && this.nextElementSibling) {
                const labelText = this.nextElementSibling.textContent.trim();
                bTipoInput.value = labelText;
                bTipoInput.classList.remove("d-none");
                console.log("Tipo selecionado:", labelText);
                
                // Chama selectSubtipo quando mudar
                selectSubtipo();
                
                // Salva no localStorage
                localStorage.setItem('selected_tipo', this.value);
            }
        }

        let timeoutSelectSubtipo; 
        async function selectSubtipo() {
            clearTimeout(timeoutSelectSubtipo); // Limpa qualquer timeout pendente
            timeoutSelectTipo = setTimeout(async () => {
                document.querySelectorAll('input[name="subtipo"]').forEach(radio => {
                    radio.addEventListener('change', function () {
                        if (this.checked) {
                            document.getElementById("b_subtipo").value = this.nextElementSibling.textContent.trim();
                        }
                        //limparSutipoHandler();
                    });
                });
                /*if (subtipoInput && subtipoInput.value && subtipoInput.value !== "Todos") {
                   limparSubtipo.classList.remove('d-none');
                } else {
                    limparSubtipo.classList.add('d-none');
                }*/
               limparSutipoHandler();
            }, 700);
        }

        function limparSutipoHandler() {
            limparSubtipo.classList.toggle('d-none', subtipoInput.value === "Todos");
        }
        //limparSutipoHandler();


        // Funções para manipular bairros no localStorage
        function salvarBairrosNoStorage() {
            const bairrosSelecionados = Array.from(document.querySelectorAll('.bairro-checkbox:checked')).map(cb => cb.value);
            if (bairrosSelecionados.length > 0) {
            localStorage.setItem('selected_bairro', JSON.stringify(bairrosSelecionados));
            } else {
            localStorage.removeItem('selected_bairro');
            }
        }

        function restaurarBairrosDoStorage() {
            const salvos = localStorage.getItem('selected_bairro');
            if (!salvos) return;
            try {
            const ids = JSON.parse(salvos);
            bairros.forEach(({ input, label }) => {
                if (ids.includes(input.value)) {
                input.checked = true;
                input.setAttribute('checked', 'checked');
                adicionarBairro(input.title, input.id);
                } else {
                input.checked = false;
                input.removeAttribute('checked');
                removerBairro(input.id);
                }
            });
            valueBairro.value = ids.length > 0 ? `${ids.length} selecionado${ids.length === 1 ? '' : 's'}` : 'Todos';
            limparBairro.classList.toggle('d-none', valueBairro.value === "Todos");
            } catch (e) {
            localStorage.removeItem('selected_bairro');
            }
        }

        function removerBairrosDoStorage() {
            localStorage.removeItem('selected_bairro');
        }

        buscaBairro.addEventListener('input', function () {
            const termo = this.value.trim().toLowerCase();
            marcador.classList.toggle('d-none', !termo);
            marcarTodos.checked = false;
            bairros.forEach(({ label, input }) => {
            if (label && input) {
                const bairroTextoOriginal = label.dataset.originalText.toLowerCase();
                const labelSemAcento = removeAcentos(bairroTextoOriginal);
                const termoSemAcento = removeAcentos(termo);

                if (labelSemAcento.includes(termoSemAcento)) {
                label.innerHTML = destacarTermo(label.dataset.originalText, termo); // Destaca o termo
                label.closest('.col-12').style.display = 'block';  // Exibe o item
                } else {
                label.closest('.col-12').style.display = 'none';  // Esconde o item
                }
            }
            });
        });

        fecharModalBtn.addEventListener('click', function () {
            resetarNomesBairros();
            limparDestaqueBairros();
        });

        function resetarNomesBairros() {
            if (!bairrosContainer) return;

            bairrosContainer.querySelectorAll('[style*="display: none"]').forEach(elemento => {
            elemento.style.display = "block";
            });

            buscaBairro.value  = "";

            bairrosContainer.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            if (checkbox.hasAttribute('checked')) {
                checkbox.checked = true; 
            } else {
                checkbox.checked = false; 
                removerBairro(checkbox.id);
            }
            });
            marcador.classList.add('d-none');
            marcarTodos.checked = false;
            bairrosContainer.click();
            limparDestaqueBairros();
        }

        function limparDestaqueBairros() {
            bairros.forEach(({ label }) => {
            if (label && label.dataset.originalText) {
                label.innerHTML = `<strong></strong>${label.dataset.originalText}`;
            }
            });
        }

        function adicionarBairro(nome, id) {
            if (!document.querySelector(`#bairro_${id}`)) {
            const div = document.createElement('div');
            div.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'border', 'border-secondary', 'px-2', 'py-1', 'rounded', 'fs-8', 'col-auto', 'gap-2');
            div.id = `bairro_${id}`;
            div.innerHTML = `<span>${nome}</span> <i class="icon-close fs-9 text-secondary btn p-0 border border-0" onclick="removerBairro('${id}')"></i>`;
            bairrosSelecionado.appendChild(div);
            }
            salvarBairrosNoStorage();
        }
        
        window.removerBairro = function(id) {
            document.querySelector(`#bairro_${id}`)?.remove();
            const checkbox = document.querySelector(`#${id}`);
            if (checkbox) {
            checkbox.checked = false;
            checkbox.removeAttribute('checked');
            }
            salvarBairrosNoStorage();
        };

        limparBtn.addEventListener('click', function () {
            marcador.classList.add('d-none');
            marcarTodos.checked = false;
            bairrosContainer.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false; 
            checkbox.removeAttribute('checked'); 
            removerBairro(checkbox.id);
            });
            valueBairro.value = 'Todos';
            limparBairro.classList.toggle('d-none', valueBairro.value === "Todos");
            buscaBairro.value = ''; 
            resetarNomesBairros();
            limparDestaqueBairros();
            removerBairrosDoStorage();
        });

        limparBairro.addEventListener('click', event => {
            event.stopPropagation();
            limparBtn.click();
        });
        
        marcarTodos.addEventListener('change', function () {
            const termo = buscaBairro.value.trim().toLowerCase();
            bairrosContainer.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            const nomeBairro = checkbox.title.toLowerCase();
            if (removeAcentos(nomeBairro).includes(removeAcentos(termo))) {
                checkbox.checked = this.checked;
                this.checked ? adicionarBairro(checkbox.title, checkbox.id) : removerBairro(checkbox.id);
            }
            });
            salvarBairrosNoStorage();
        });

        aplicarBtn.addEventListener('click', function () {
            const selecionados = [...document.querySelectorAll('.bairro-checkbox:checked')];
            valueBairro.value = selecionados.length > 0 ? `${selecionados.length} selecionado${selecionados.length === 1 ? '' : 's'}` : 'Todos';

            selecionados.forEach(cb => cb.setAttribute('checked', 'checked'));

            resetarNomesBairros();
            buscaBairro.value = '';
            marcador.classList.add('d-none');

            if(valueBairro.value != "Todos"){
            limparBairro.classList.remove('d-none');
            }
            limparDestaqueBairros();
            salvarBairrosNoStorage();
        });

        bairrosContainer.addEventListener('click', function (event) {
            if (event.target.classList.contains('bairro-checkbox')) {
            if (event.target.checked) {
                adicionarBairro(event.target.title, event.target.id);
            } else {
                removerBairro(event.target.id);
            }
            }
        });

        // Restaurar bairros selecionados ao carregar
        // Chama restaurarBairrosDoStorage sempre que bairros são atualizados
        // Isso garante que ao recarregar (Ctrl+F5) ou voltar para a principal, os bairros marcados são restaurados
        const _originalAtualizarBairros = atualizarBairros;
        atualizarBairros = function(data) {
            _originalAtualizarBairros(data);
            setTimeout(restaurarBairrosDoStorage, 0);
        };
        // Se bairros já estiverem carregados (ex: ao voltar do histórico), restaura imediatamente
        if (bairros.length > 0) {
            restaurarBairrosDoStorage();
        }

        // Busca nomes das cidades
        const buscaLocal = document.querySelector('#buscaLocal');
        const namecity = document.querySelectorAll('.nameCity');
        const cidadeLabels = document.querySelectorAll('.cidade');
        const localInput = document.getElementById('b_cidade'); // Campo de entrada para a cidade/estado
        const limparLocalIcon = document.querySelector('.limparLocal');

        buscaLocal.addEventListener('input', () => {
            indexAtivo = -1; // Reset ao digitar
            destacarItem(indexAtivo); // Remove qualquer destaque
        });
        
        // Função para remover acentos de uma string
        function removeAcentos(str) {
            return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        // Função para adicionar negrito nas correspondências
        function destacarTermo(cidadeTexto, termo) {
            const regex = new RegExp(`(${termo})`, "gi");
            return cidadeTexto.replace(regex, "<strong>$1</strong>");
        }

        document.getElementById('groupCity').addEventListener('click', function () {
            // Só foca se estiver visível
            if (buscaLocal && !buscaLocal.hasAttribute('readonly')) {
                buscaLocal.focus();
            }
        });

        const groupCityEl = document.getElementById('groupCity');
        groupCityEl.addEventListener('hide.bs.dropdown', () => {
            buscaLocal.value = '';

            cidadeLabels.forEach(label => {
                const nomeCompleto = label.textContent.trim(); // Apenas remove espaços extras no início e fim
                label.innerHTML = `<strong></strong>${nomeCompleto}`;
            });

            document.querySelectorAll('label.nameCity').forEach(label => {
                label.classList.remove('d-none');
                if (!label.classList.contains('d-flex')) {
                    label.classList.add('d-flex');
                }
            });

            document.querySelectorAll('label.nameState').forEach(label => {
                const inputId = label.getAttribute('for');
                const input = document.getElementById(inputId);

                if (input && input.checked) {
                    label.classList.remove('d-none');
                    label.classList.add('d-flex');
                } else {
                    label.classList.remove('d-flex');
                    label.classList.add('d-none');
                }
            });


        });

        buscaLocal.addEventListener('input', function () {
            const termo = removeAcentos(this.value.trim().toLowerCase());

            namecity.forEach((cidadeLabel) => {
                const cidadeSpan = cidadeLabel.querySelector('.cidade');
                const cidadeTexto = cidadeSpan.textContent.trim();
                const cidadeLower = removeAcentos(cidadeTexto.toLowerCase());

                const temVirgulaBrasil = cidadeTexto.toLowerCase().includes(', brasil');
                const termoEhParteDeBrasil = 'brasil'.startsWith(termo);

                if (temVirgulaBrasil && termoEhParteDeBrasil) {
                    cidadeLabel.classList.add('d-none');
                    cidadeLabel.classList.remove('d-flex');
                    return;
                }


                if (cidadeLower.includes(termo)) {
                    cidadeLabel.classList.remove('d-none');
                    cidadeLabel.classList.add('d-flex');

                    const cidadeDestacada = destacarTermo(cidadeTexto, termo);
                    cidadeLabel.querySelector('.cidade').innerHTML = cidadeDestacada;
                } else {
                    cidadeLabel.classList.remove('d-flex');
                    cidadeLabel.classList.add('d-none');
                }
            });
        });

        // Evento para atualizar o campo 'local' ao selecionar uma opção
       document.querySelectorAll('.btn-check[name="local"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (this.checked) {
                    const labelSelecionado = document.querySelector(`label[for="${this.id}"]`);
                    if (!labelSelecionado) return;

                    const cidade = labelSelecionado.querySelector('.cidade').textContent.trim();
                    const estado = labelSelecionado.querySelector('.estado').textContent.trim();
                    const valorFormatado = estado ? `${cidade} - ${estado}` : cidade;

                    localInput.value = valorFormatado;
                    localStorage.setItem('selected_city', valorFormatado);

                    // Oculta todos os labels com class .nameState
                    document.querySelectorAll('label.nameState').forEach(label => {
                        label.classList.remove('d-flex');
                        label.classList.add('d-none');
                    });

                    // Mostra apenas o selecionado e remove a classe nameState
                    labelSelecionado.classList.remove('d-none', 'nameState');
                    labelSelecionado.classList.add('d-flex');
                }
                limparBtn.click(); // Limpa os bairros selecionados ao mudar a cidade
            });
        });

        // Verifica se há cidade salva no localStorage
        const savedCity = localStorage.getItem('selected_city');


        if (savedCity) {
            const radios = document.querySelectorAll('.btn-check[name="local"]');

            document.querySelectorAll('label.nameState').forEach(label => {
                label.classList.remove('d-flex');
                label.classList.add('d-none');
            });
            radios.forEach(function (radio) {
                const label = document.querySelector(`label[for="${radio.id}"]`);
                const cidade = label.querySelector('.cidade')?.textContent.trim();
                const estado = label.querySelector('.estado')?.textContent.trim();
                const valorRadio = estado ? `${cidade} - ${estado}` : cidade;

            if (valorRadio.toLowerCase() === savedCity.toLowerCase()) {
                radio.checked = true;
                localInput.value = savedCity;

                // Mostra o selecionado e remove nameState
                label.classList.remove('d-none', 'nameState');
                label.classList.add('d-flex');
            }

            });
        } else {
            const radioChecked = document.querySelector('.btn-check[name="local"]:checked');
            if (radioChecked) {
                const label = document.querySelector(`label[for="${radioChecked.id}"]`);
                const cidade = label.querySelector('.cidade').textContent.trim();
                const estado = label.querySelector('.estado').textContent.trim();
                localInput.value = estado ? `${cidade} - ${estado}` : cidade;
            }
        }

            function atualizarBotaoLimpar() {
                if (localInput.value.trim().toLowerCase() !== 'todos') {
                    limparLocalIcon.classList.remove('d-none');
                } else {
                    limparLocalIcon.classList.add('d-none');
                }
            }

            // Exibe botão se necessário ao carregar
            atualizarBotaoLimpar();

            // Evento de clique no botão limpar
            limparLocalIcon.addEventListener('click', function () {
                localStorage.removeItem('selected_city');

                const radioSelecionado = document.querySelector('input[type="radio"][name="local"]:checked');
                if (radioSelecionado) {
                    radioSelecionado.checked = false;
                }

                localInput.value = 'Todos';
                limparLocalIcon.classList.add('d-none');
        
                selectBairro.removeAttribute("data-bs-toggle");
                selectBairro.removeAttribute("data-bs-target");
                selectBairro.setAttribute("disabled", "disabled");
                selectBairro.classList.add("bg-body-secondary");
                document.querySelector('.valueBairro').value = 'Todos';
                document.querySelector('#bairrosSelecionados').innerHTML = '';
                limparBtn.click(); 
            });

        // Final Busca nomes das cidades

        // Seleciona os inputs e radio buttons Tipo e subtipo
        //Tipos e Subtipos
        const tipoInput = document.getElementById("b_tipo");
        //const subtipoInput = document.querySelector('#b_subtipo');
        const tipoRadios = document.querySelectorAll('input[name="tipo"]');
        const subtipoRadios = document.querySelectorAll('input[name="subtipo"]');
        //const limparSubtipo = document.querySelector(".limparSubtipo");      
        const dropdownTipo = document.querySelectorAll(".dropdown-tipo");

        const labelDormitorios = document.querySelector(".labelDormitorios");
        const groupAmbient = document.querySelector(".groupAmbiente");
        const groupDormitorio = document.querySelector(".groupDormitorio");
        const ambienteCheckboxs = document.querySelectorAll("input[name='ambiente']");
        const quartoCheckboxs = document.querySelectorAll("input[name='quarto']");
        const suiteCheckboxs = document.querySelectorAll("input[name='suite']");
        const exataCheckboxs = document.querySelectorAll("input[name='exata']");
        const btnAplicar = document.querySelector(".btnAplicar");
        const dropdownDormitorios = document.querySelector(".dropdown-dormitorios");
        const selectDormitorios = document.querySelector(".select-dormitorios");
        const inputDormitorios = document.querySelector("#dormitorios");
        const limparDormitorio = document.querySelector(".limparDormitorio");

        
        const exataCheckbox = document.querySelector('#exata');
        const suiteLabels = document.querySelectorAll('[for^="suite"]');
        const quartoLabels = document.querySelectorAll('[for^="quarto"]');
        const ambienteLabels = document.querySelectorAll('[for^="ambiente"]');
        const groupDormiAmbiente = document.getElementById('groupDormiAmbiente')
        
    
        // Atualiza o campo tipo com o rádio selecionado ao carregar a página
        const selectedTipo = document.querySelector('input[name="tipo"]:checked');
        if (selectedTipo) {
            tipoInput.value = selectedTipo.nextElementSibling.textContent.trim();
        }
    
        // Atualiza o campo subtipo com o rádio selecionado ao carregar a página
        const selectedSubtipo = document.querySelector('input[name="subtipo"]:checked');
        if (selectedSubtipo) {
            subtipoInput.value = selectedSubtipo.nextElementSibling.textContent.trim();
        }
    
        // Função para atualizar o valor de subtipo
        subtipoRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.checked) {
                    subtipoInput.value = this.nextElementSibling.textContent.trim();
                }
                limparSutipoHandler();
            });
            
        });


        limparSubtipo.addEventListener('click', event => {
            const subtipoRadios = document.querySelectorAll('input[name="subtipo"]');
            subtipoInput.value = "Todos";
            
            // Desmarca todos os botões de rádio
            subtipoRadios.forEach(radio => {
                radio.checked = false;
            });

            localStorage.removeItem('selected_subtipo');
            limparSutipoHandler();
        });

        dropdownTipo.forEach(radio => {
            radio.addEventListener("click", function() {
                limparSubtipo.click();
                limparDormitorio.click();
                atualizarEstadoDormitorios();
            });
        });

        // Adicionando o evento de clique para limpar
        // Funções para manipular localStorage dos filtros de dormitórios
        function salvarDormitoriosNoStorage() {
            // Salva o valor do quarto, suite, ambiente e exata selecionados
            const quartoSelecionado = Array.from(quartoCheckboxs).find(cb => cb.checked)?.value || "";
            const suiteSelecionado = Array.from(suiteCheckboxs).find(cb => cb.checked)?.value || "";
            const ambienteSelecionado = Array.from(ambienteCheckboxs).find(cb => cb.checked)?.value || "";
            const exataSelecionado = exataCheckbox.checked ? "1" : "";

            if (quartoSelecionado) {
            localStorage.setItem('selected_quarto', quartoSelecionado);
            } else {
            localStorage.removeItem('selected_quarto');
            }

            if (suiteSelecionado) {
            localStorage.setItem('selected_suite', suiteSelecionado);
            } else {
            localStorage.removeItem('selected_suite');
            }

            if (ambienteSelecionado) {
            localStorage.setItem('selected_ambiente', ambienteSelecionado);
            } else {
            localStorage.removeItem('selected_ambiente');
            }

            if (exataSelecionado) {
            localStorage.setItem('selected_exata', exataSelecionado);
            } else {
            localStorage.removeItem('selected_exata');
            }
        }

        function restaurarDormitoriosDoStorage() {
            const quartoSalvo = localStorage.getItem('selected_quarto');
            const suiteSalvo = localStorage.getItem('selected_suite');
            const ambienteSalvo = localStorage.getItem('selected_ambiente');
            const exataSalvo = localStorage.getItem('selected_exata');

            let algumMarcado = false;

            quartoCheckboxs.forEach(cb => {
            cb.checked = cb.value === quartoSalvo;
            if (cb.checked) {
                cb.setAttribute("checked", "checked");
                algumMarcado = true;
            } else {
                cb.removeAttribute("checked");
            }
            });
            suiteCheckboxs.forEach(cb => {
            cb.checked = cb.value === suiteSalvo;
            if (cb.checked) {
                cb.setAttribute("checked", "checked");
                algumMarcado = true;
            } else {
                cb.removeAttribute("checked");
            }
            });
            ambienteCheckboxs.forEach(cb => {
            cb.checked = cb.value === ambienteSalvo;
            if (cb.checked) {
                cb.setAttribute("checked", "checked");
                algumMarcado = true;
            } else {
                cb.removeAttribute("checked");
            }
            });
            exataCheckbox.checked = !!exataSalvo;
            if (exataCheckbox.checked) {
            exataCheckbox.setAttribute("checked", "checked");
            algumMarcado = true;
            } else {
            exataCheckbox.removeAttribute("checked");
            }

            if (algumMarcado) {
            limparDormitorio.classList.remove('d-none');
            } else {
            limparDormitorio.classList.add('d-none');
            }
        }

        function limparDormitoriosDoStorage() {
            localStorage.removeItem('selected_quarto');
            localStorage.removeItem('selected_suite');
            localStorage.removeItem('selected_ambiente');
            localStorage.removeItem('selected_exata');
            // Também limpa o campo de dormitórios para "Todos"
            const inputDormitorios = document.getElementById('dormitorios');
            if (inputDormitorios) {
            inputDormitorios.value = "Todos";
            }
            // Remove checked dos checkboxes de suite, quarto e ambiente
            document.querySelectorAll('input[name="suite"], input[name="quarto"], input[name="ambiente"]').forEach(cb => {
                cb.checked = false;
                cb.removeAttribute('checked');
            });
            // Remove checked do exata também
            const exata = document.querySelector('input[name="exata"]');
            if (exata) {
                exata.checked = false;
                exata.removeAttribute('checked');
            }
        }

        // Ao clicar em aplicar, salva no localStorage
        btnAplicar.addEventListener("click", function () {
            salvarDormitoriosNoStorage();
        });

        // Ao clicar em limpar, remove do localStorage
        limparDormitorio.addEventListener('click', event => {
            limparDormitoriosDoStorage();
        });

        // Ao carregar a página, restaura do localStorage
        restaurarDormitoriosDoStorage();

        // Ao trocar de cidade, limpa o localStorage dos dormitórios
        document.getElementById('city').addEventListener('change', function () {
            limparDormitoriosDoStorage();
        });

        limparDormitorio.addEventListener('click', event => {
            event.stopPropagation();
            inputDormitorios.value = "Todos";
            setTimeout(() => {
            ambienteCheckboxs.forEach(ambiente => {
                ambiente.checked = false;
                ambiente.removeAttribute("checked");
                exataCheckbox.checked = false;
                exataCheckbox.removeAttribute("checked");
            });
        
            limparDormitorioHandler();
            }, 300);
            const dropdown = new bootstrap.Dropdown(selectDormitorios);
            dropdown.hide();
        });

        document.getElementById('city').addEventListener('change', function () {
            atualizarEstadoDormitorios();
            limparDormitorio.click();
        });


        let suiteCount = 0;
        let quartoCount = 0;
        let ambienteCount = 0;
        //let estadoInicial = {};

        suiteCheckboxs.forEach(suite => {
            if (suite.checked) {
            suiteCount = parseInt(suite.value);
            suite.setAttribute("checked", "checked"); // Mantém marcado
            } 
        });
        quartoCheckboxs.forEach(quarto => {
            if (quarto.checked) {
            quartoCount = parseInt(quarto.value);
            quarto.setAttribute("checked", "checked"); // Mantém marcado
            } 
        });
        ambienteCheckboxs.forEach(ambiente => {
            if (ambiente.checked) {
            ambienteCount = parseInt(ambiente.value);
            ambiente.setAttribute("checked", "checked"); // Mantém marcado
            } 
        });



        function restaurarEstadoInicial() {
            ambienteCheckboxs.forEach(checkbox =>   { checkbox.checked = checkbox.hasAttribute("checked");});
            quartoCheckboxs.forEach(checkbox =>     { checkbox.checked = checkbox.hasAttribute("checked");});
            suiteCheckboxs.forEach(checkbox =>      { checkbox.checked = checkbox.hasAttribute("checked");});
            exataCheckbox.checked = exataCheckbox.hasAttribute("checked");
            
        }


        // Monitora o fechamento do dropdown
        groupDormiAmbiente.addEventListener("hide.bs.dropdown", function (event) {
            if (!event.target.contains(btnAplicar)) {
            restaurarEstadoInicial();
            }
        });

        groupDormiAmbiente.addEventListener("show.bs.dropdown", function () {
            atualizarLabels();
        });

        // Atualiza o estado inicial quando o botão Aplicar for clicado
        btnAplicar.addEventListener("click", function () {
           // salvarEstadoInicial();
            suiteCount = 0;
            quartoCount = 0;
            ambienteCount = 0;

            if(tipoInput.value === "Residenciais"){
            suiteCheckboxs.forEach(suite => {
                if (suite.checked) {
                suiteCount = parseInt(suite.value);
                suite.setAttribute("checked", "checked"); // Mantém marcado
                } else {
                suite.removeAttribute("checked"); // Remove se não estiver marcado
                }
            });
            quartoCheckboxs.forEach(quarto => {
                if (quarto.checked) {
                quartoCount = parseInt(quarto.value);
                quarto.setAttribute("checked", "checked"); // Mantém marcado
                } else {
                quarto.removeAttribute("checked"); // Remove se não estiver marcado
                }
            });
            updateDormitoriosValue();
            } else {
            ambienteCheckboxs.forEach(ambiente => {
            if (ambiente.checked) {
                ambienteCount = parseInt(ambiente.value);
                ambiente.setAttribute("checked", "checked"); // Mantém marcado
            } else {
                ambiente.removeAttribute("checked"); // Remove se não estiver marcado
            }
            });
            updateAmbientesValue() 
            }
            exataCheckboxs.forEach(quarto => {
            if (exata.checked) {
                exata.setAttribute("checked", "checked"); // Mantém marcado
            } else {
                exata.removeAttribute("checked"); // Remove se não estiver marcado
            }
            });

            limparDormitorioHandler();

            const dropdown = new bootstrap.Dropdown(selectDormitorios);
            dropdown.hide()
        });

        function updateDormitoriosValue() {
            const suiteValue = suiteCount > 0 ? `${suiteCount} Suíte${suiteCount > 1 ? 's' : ''}` : '';
            const quartoValue = quartoCount > 0 ? `${quartoCount} Quarto${quartoCount > 1 ? 's' : ''}` : '';

            let value = '';
            if (suiteValue && quartoValue) {
            value = `${suiteValue} + ${quartoValue}`;
            } else {
            value = suiteValue || quartoValue;
            }
            inputDormitorios.value = value || 'Todos';
        }

        function updateAmbientesValue() {
            const ambienteValue = ambienteCount > 0 ? `${ambienteCount} Ambiente${ambienteCount > 1 ? 's' : ''}` : '';
            inputDormitorios.value = ambienteValue  || 'Todos';
        }

        // Função para atualizar as labels
        function atualizarLabels() {
            const exataMarcado = exataCheckbox.checked;
        
            // Atualiza as labels de suítes
            suiteLabels.forEach(label => {
            const labelText = label.textContent.trim();
            if (exataMarcado && labelText.includes('+')) {
                label.textContent = labelText.replace('+', '');
            } else if (!exataMarcado && !labelText.includes('+')) {
                label.textContent = `${labelText}+`;
            }
            });
        
            // Atualiza as labels de quartos
            quartoLabels.forEach(label => {
            const labelText = label.textContent.trim();
            if (exataMarcado && labelText.includes('+')) {
                label.textContent = labelText.replace('+', '');
            } else if (!exataMarcado && !labelText.includes('+')) {
                label.textContent = `${labelText}+`;
            }
            });
        
            // Atualiza as labels de ambientes
            ambienteLabels.forEach(label => {
            const labelText = label.textContent.trim();
            if (exataMarcado && labelText.includes('+')) {
                label.textContent = labelText.replace('+', '');
            } else if (!exataMarcado && !labelText.includes('+')) {
                label.textContent = `${labelText}+`;
            }
            });
        }
        

        // Adiciona o evento para atualizar as labels quando o checkbox "exata" for alterado
        exataCheckbox.addEventListener('change', atualizarLabels);

        // Função debounce robusta para garantir execução da última chamada
        let timeoutAtualizarEstado;
        function atualizarEstadoDormitorios() {
            if (timeoutAtualizarEstado) {
            clearTimeout(timeoutAtualizarEstado);
            }
            // Aumente o tempo do debounce para evitar "pular" (ex: 700ms)
            timeoutAtualizarEstado = setTimeout(() => {
                timeoutAtualizarEstado = null; // Limpa referência para evitar bloqueio futuro
                // Aguarda até que o input[name='tipo'] esteja disponível e selecionado
                let tipoValor = "";
                let tentativas = 0;
                const maxTentativas = 10;

            function waitForTipoSelecionado(callback) {
                let tentativas = 0;
                const maxTentativas = 10;
                function tentar() {
                    const tipoSelecionado = document.querySelector("input[name='tipo']:checked");
                    if (tipoSelecionado && tipoSelecionado.nextElementSibling) {
                        callback(tipoSelecionado.nextElementSibling.textContent.trim());
                    } else if (tentativas < maxTentativas) {
                        tentativas++;
                        setTimeout(tentar, 100);
                    } else {
                        callback("");
                    }
                }
                tentar();
            }

            inputDormitorios.value = "Todos";
            waitForTipoSelecionado(function(tipoValor) {
                const tipoValorStr = (typeof tipoValor === "string" ? tipoValor : "").trim().toLowerCase();

                if (["comerciais", "negócios e investimentos", "imóveis para lazer"].includes(tipoValorStr)) {
                    selectDormitorios.removeAttribute("disabled");
                    selectDormitorios.classList.remove("bg-body-secondary");
                    labelDormitorios.textContent = "Ambientes";
                    groupAmbient?.classList.remove("d-none");
                    groupDormitorio?.classList.add("d-none");
                    ambienteCheckboxs.forEach(ambiente => ambiente.removeAttribute("disabled"));
                    quartoCheckboxs.forEach(quarto => quarto.setAttribute("disabled", "disabled"));
                    suiteCheckboxs.forEach(suite => suite.setAttribute("disabled", "disabled"));
                    inputDormitorios.value = 'Todos';
                    updateAmbientesValue();
                } else if (tipoValorStr === "residenciais") {
                    selectDormitorios.removeAttribute("disabled");
                    selectDormitorios.classList.remove("bg-body-secondary");
                    labelDormitorios.textContent = "Dormitórios";
                    groupDormitorio?.classList.remove("d-none");
                    groupAmbient?.classList.add("d-none");
                    ambienteCheckboxs.forEach(ambiente => ambiente.setAttribute("disabled", "disabled"));
                    quartoCheckboxs.forEach(quarto => quarto.removeAttribute("disabled"));
                    suiteCheckboxs.forEach(suite => suite.removeAttribute("disabled"));
                    inputDormitorios.value = 'Todos';
                    updateDormitoriosValue();
                } else {
                    selectDormitorios.setAttribute("disabled", "disabled");
                    selectDormitorios.classList.add("bg-body-secondary");
                    labelDormitorios.textContent = "Dormitórios";
                    quartoCheckboxs.forEach(quarto => quarto.setAttribute("disabled", "disabled"));
                    suiteCheckboxs.forEach(suite => suite.setAttribute("disabled", "disabled"));
                    suiteCount = 0;
                    quartoCount = 0;
                    ambienteCount = 0;
                    inputDormitorios.value = 'Todos';
                }
                // Sempre que atualizar o estado, restaura do localStorage
                //console.log("Codigo2: "+tipoValor);
                restaurarDormitoriosDoStorage();
            });
            }, 150); // Aumentado para 700ms para evitar pular
        }

        atualizarEstadoDormitorios();


        function clearDormitoriosCheckboxs() {
            const checkboxs = dropdownDormitorios.querySelectorAll("input[type='checkbox']");
            checkboxs.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.removeAttribute("checked");
            });
        }

        function allowSingleSelection(checkboxes) {
            checkboxes.forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                if (this.checked) {
                checkboxes.forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
                }
            });
            });
        }

        // Aplicar a lógica para os grupos de checkboxes
        allowSingleSelection(quartoCheckboxs);
        allowSingleSelection(suiteCheckboxs);
        allowSingleSelection(ambienteCheckboxs);

        function limparDormitorioHandler() {
            limparDormitorio.classList.toggle('d-none', inputDormitorios.value === "Todos");
        }
        limparDormitorioHandler();
        // Final os inputs e radio buttons Tipo e subtipo

        // Adiciona mensagem da palavra-chave
        const inputPalavra = document.getElementById("palavra");
        if(!inputPalavra){
            return
        }
        const dropdownMenu = inputPalavra.closest(".select-container").querySelector(".dropdown-menu");
        const selectContainer = inputPalavra.closest(".select-container");
        
        inputPalavra.addEventListener("input", function () {
            if (this.value.trim().length > 0) {
                dropdownMenu.classList.remove("show");
            } else {
                dropdownMenu.classList.add("show");
            }
        });
        // Final Adiciona mensagem da palavra-chave 
    });

    // Função para formatar cada trecho (sem acento, lowercase, hífens nos espaços)
    function formatarTexto(texto) {
        return texto
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "") // Remove acentos
            .toLowerCase()
            .trim()
            .replace(/\s+/g, "-"); // Substitui espaços por hífen
    }

    // Função para remover acentos e espaços
    function slugifyDormitorios(texto) {
        return texto
            .split("+")
            .map(part => formatarTexto(part))
            .join("/"); // cada parte vira um segmento da URL
    }

    document.querySelectorAll('#FormPesquisa input').forEach(input => {
        input.addEventListener('click', () => {
          const form = document.getElementById('FormPesquisa');
          form.classList.remove('was-validated');
        });
    });

    function converterValor(valor) {
        let str = valor.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
        str = str.replace(/\./g, "").trim();
        const numeros = str.match(/\d+/g);
    
        if (str.includes("ate") && numeros?.length === 1) {
            return `ate-r$-${numeros[0]}`;
        }
    
        if (str.includes("a partir") && numeros?.length === 1) {
            return `a-partir-de-r$-${numeros[0]}`;
        }
    
        if (numeros?.length === 2) {
            return `de-r$-${numeros[0]}-ate-${numeros[1]}`;
        }
        return valor; 
    }
    
    document.getElementById("FormPesquisa").addEventListener("submit", function (event) {
        event.preventDefault();

        const btnBuscar = document.getElementById("btnBuscar");
        const spanBuscar = btnBuscar.querySelector(".buscar");
        const spanBuscando = btnBuscar.querySelector(".buscando");

        // Feedback visual imediato
        btnBuscar.disabled = true;
        if (spanBuscar) spanBuscar.classList.add("visually-hidden");
        if (spanBuscando) spanBuscando.classList.remove("visually-hidden");

        //setTimeout(() => {
        try {
            const form = document.querySelector(".needs-validation");
            const selectContainers = document.querySelectorAll(".select-container");
            const negocio = document.querySelector('[name="b_negocio"]:checked');
            const cidade = document.querySelector('[name="local"]:checked');
            const tipo = document.getElementById("b_tipo");
            const subtipo = document.getElementById("b_subtipo");
            const bairro = Array.from(document.querySelectorAll('[name="b_bairro[]"]:checked')).map(cb => cb.value).join(',');
            const dormitorios = document.getElementById("dormitorios").value.trim();
            const slugDormitorios = slugifyDormitorios(dormitorios);
            const exata = document.querySelector('[name="exata"]:checked');
            const valor = document.getElementById("valor").value.trim();
            const urbano = document.getElementById("b_palavrachave").value.trim()
            const rural = document.getElementById("b_palavrachaveRural").value.trim();
            const palavraChave = rural || urbano;
            
            let negocioValor = negocio ? negocio.value : null;

            // Função para resetar botão (inline)
            function resetBotao() {
                btnBuscar.disabled = false;
                if (spanBuscar) spanBuscar.classList.remove("visually-hidden");
                if (spanBuscando) spanBuscando.classList.add("visually-hidden");
            }

         /*   // Validação rápida
            let isValid = true;
            selectContainers.forEach(selectContainer => {
                const input = selectContainer.querySelector("input");
                if (input.required && !input.value.trim()) {
                    selectContainer.classList.add("is-invalid");
                    isValid = false;
                } else {
                    selectContainer.classList.remove("is-invalid");
                }
            });

            form.classList.add("was-validated");

            if (!isValid) {
                resetBotao();
                return;
            }

            if (!negocio) {
                alert("Por favor, selecione um tipo de negócio.");
                resetBotao();
                return;
            }
*/

            if (palavraChave && /^\d+$/.test(palavraChave)) {
                const url = `/imovel/${palavraChave}`;
                window.location = url;  
                return;

            } else if (negocioValor == "Rural") {
                const pesquisa = document.querySelector('[name="pesquisa"]:checked');
                const palavraChaveRural = document.getElementById("b_palavrachaveRural")?.value.trim() || "";
                const negociacao = document.getElementById("b_negociacao")?.value.trim() || "";
                const subtiporural = document.getElementById("b_subtiporural")?.value.trim() || "";
                const valoRural = document.getElementById("valorRural")?.value.trim() || "";
                const solo = Array.from(document.querySelectorAll('[name="b_solo[]"]:checked')).map(cb => cb.value).join(',');
                const cultivo = Array.from(document.querySelectorAll('[name="b_cultivo[]"]:checked')).map(cb => cb.value).join(',');

                // CORREÇÃO: declare url antes do if/else
                let url = "";
                if(subtiporural !== "Todos") {
                    url = `/imoveis/venda/${formatarTexto(subtiporural)}`;
                } else {
                    url = `/imoveis/venda/${formatarTexto(negocioValor).replace('rural', 'rurais')}`;
                }

                if(pesquisa && pesquisa.value === 'Cidade'){
                    const cidade = document.querySelector('[name="ruralcity"]:checked');
                    if (cidade) {
                        if (cidade.dataset.local === "ruralCity-todas-as-cidades-br") {
                            url += `/brasil-br`;
                        } else {
                            url += `/${cidade.dataset.local.replace('ruralCity-', '')}`;
                        }
                    } else {
                        url += `/brasil-br`;
                    }
                } else if (pesquisa && pesquisa.value === 'Bioma'){
                    const bioma = Array.from(document.querySelectorAll('[name="b_bioma[]"]:checked')).map(cb => cb.value).join(',');
                    url += `/brasil-br`;
                    if(bioma) {
                        url += `/${formatarTexto(bioma).replace("-","_")}-bioma`;
                    }
                } else if (pesquisa && pesquisa.value === 'Estado'){
                    const estado = document.querySelector('[name="state"]:checked');
                    if (estado) {
                        if (estado.dataset.local === "rural-todos-os-estados-br") {
                            url += `/brasil-br`;
                        } else {
                            url += `/${estado.dataset.local.replace('rural-', '')}`;
                        }
                    } else {
                        url += `/brasil-br`;
                    }
                } else {
                    url += `/brasil-br`;
                }

                if(negociacao !== "Todos") {
                    url += `/${formatarTexto(negociacao)}-negociacao`;
                }

                if(valoRural !== "Todos") {
                    url += `/${converterValor(valoRural)}`;
                }

                if(solo) {
                    url += `/${formatarTexto(solo).replace("-","_")}-solo`;
                }

                if(cultivo) {
                    url += `/${formatarTexto(cultivo).replace(/-/g, "_")}-cultivo`;
                }

                if(palavraChaveRural){
                    url += `/palavra-chave-${formatarTexto(palavraChaveRural)}`;
                }
                window.location = url;
            } else {
                let isValid = true;
                selectContainers.forEach(selectContainer => {
                    const input = selectContainer.querySelector("input");
                    if (input.required) {
                        if (!input.value.trim()) {
                        selectContainer.classList.add("is-invalid");
                        isValid = false;
                        } else {
                        selectContainer.classList.remove("is-invalid");
                        }
                    }
                });

                form.classList.add("was-validated");

                if (!isValid) {
                    resetBotao();
                    return;
                }

                if (!negocio) {
                    alert("Por favor, selecione um tipo de negócio.");
                    resetBotao();
                    return;
                }

                // Define o tipo único
                const tipoUnico = tipo.value && subtipo.value !== "Todos" ? subtipo.value.replace(/,/g, "") : tipo.value;

                let url = `/imoveis/${formatarTexto(negocio.value)}`;

                if (tipoUnico) {
                    url += `/${formatarTexto(tipoUnico)}`;
                }

                if (cidade) {
                    url += `/${cidade.dataset.local}`;
                } else {
                    url += `/brasil-br`;
                }

                if(bairro) {
                    url += `/${bairro}-bairros`;
                }
                if(valor !== "Todos") {
                    url += `/${converterValor(valor)}`;
                }

                if (dormitorios !== "Todos") {
                    if (tipo.value.trim() === "Residenciais") {
                        url += `/${slugDormitorios}`;
                    } else if (tipo.value.trim() === "Comerciais") {
                        // Trata também valores como "1 Ambiente", "3 Ambientes", etc.
                        url += `/${slugDormitorios}`;
                    }
                    if(exata && slugDormitorios){
                        url += `/quantidades_exatas`;
                    }
                }
                if(palavraChave){
                    url += `/palavra-chave-${formatarTexto(palavraChave)}`;
                }
                window.location = url;
            }
        //}, 500);
        } catch (error) {
            console.error('Erro na busca:', error);
            //resetBotaoBuscar();
             btnBuscar.disabled = false;
            if (spanBuscar) spanBuscar.classList.remove("visually-hidden");
            if (spanBuscando) spanBuscando.classList.add("visually-hidden");
        }
        
        //function resetBotaoBuscar() {
        //    btnBuscar.disabled = false;
        //}

    });

    
window.addEventListener("pageshow", function (event) {
    // Timeout para garantir que a página terminou de carregar
    setTimeout(() => {
        const btnBuscar = document.getElementById("btnBuscar");
        if (!btnBuscar) return;

        const spanBuscar = btnBuscar.querySelector(".buscar");
        const spanBuscando = btnBuscar.querySelector(".buscando");

        btnBuscar.disabled = false;
        
        if (spanBuscar) {
            spanBuscar.classList.remove("visually-hidden");
        }
        if (spanBuscando) {
            spanBuscando.classList.add("visually-hidden");
        }
    }, 100); // 100ms de delay para garantir que tudo carregou
});


let jaExecutou = false;

function handlePageActive() {

    if (jaExecutou) return;

    if (localStorage.getItem('precisaAtualizar') === 'true') {
        jaExecutou = true;
        localStorage.setItem('precisaAtualizar', 'false'); 
        
        const loading = document.getElementById('loading');
        if (loading) {
            loading.classList.remove('d-none');
            loading.classList.add('d-flex');
        }

        ['#b_cidade', '#b_subtipo', '#valor', '#dormitorios', '.valueBairro', '#valorRural', '#b_subtiporural', '#b_negociacao', '#valorRural', '#valueSolo', '#valueCultivo', '#valorAluguel'].forEach(function(selector) {
            var el = document.querySelector(selector);
            if (el) {
                el.value = "Todos";
            }
        });

        ['#b_tipo','#b_palavrachaveRural', '#b_palavrachave', "#b_checkin", '#b_checkout', '#b_pessoa'].forEach(function(selector) {
            var el = document.querySelector(selector);
            if (el) {
                el.value = "";
            }
        });

        location.reload();
    }
}

const isMobile = /Android|iPhone|iPad|iPod|iOS/i.test(navigator.userAgent);

if (isMobile) {
   window.addEventListener('pageshow', function (event) {
        if (localStorage.getItem('precisaAtualizar') === 'true') {    
            handlePageActive();
            localStorage.setItem('precisaAtualizar', 'false'); 
            console.log('Dados foram alterados.');
        } else {
            console.log('Dados não foram alterados. Não precisa atualizar.');
        }
    });
} else {
    window.addEventListener('focus', handlePageActive);
}


window.addEventListener("pageshow", function () {
    const btnBuscar = document.getElementById("btnBuscar");
    if (!btnBuscar) return;

    //const spanBuscar = btnBuscar.querySelector(".buscar");
    //const spanBuscando = btnBuscar.querySelector(".buscando");

    btnBuscar.disabled = false;
    //if (spanBuscar) spanBuscar.classList.remove("visually-hidden");
    //if (spanBuscando) spanBuscando.classList.add("visually-hidden");
});


