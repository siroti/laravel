<header class="position-fixed top-0 w-100 z-3 shadow-sm">
    <nav class="navbar py-4 px-2 px-md-0">
      <div class="container">
        <div class="col">
          <a href="/" aria-label="SUB100 Imobiliárias" title="SUB100 Imobiliárias">
            <img class="logo" loading="lazy" src="{{ getImageLogo() }}" alt="{{$config['general']['site_title']}}"/>
          </a>
        </div>
        <div class="col-auto">
          <ul class="d-flex align-items-center gap-3 mb-0 dropdown-center">
            <li class="nav-item d-none d-md-block"> 
              <button type="button" class="nav-link" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" aria-label="WhatsApp">
                <i class="icon-whatsapp fs-30" title="Contato" aria-label="Contato"></i>
              </button>
              @include('header.partials.contact-dropdownBCK')
            </li>
            <li class="nav-item position-relative d-none d-md-block">
              <a class="nav-link" href="/imoveis/favoritos" aria-label="Favoritos" >
                <i class="icon-favorito fs-30" title="Icone Favoritos" aria-label="Icone Favoritos"></i>
                <span id="contador-favoritos" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger px-2 py-1" aria-label="0 Favoritos">0</span>
              </a>
            </li>
            <li class="nav-item d-none d-md-block">
              <button type="button" class="nav-link" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" aria-label="Portal">
                <i class="icon-portal fs-30" title="Icone Portal" aria-label="Icone Portal"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end rounded-3 p-4 border shadow mt-1 me-n5 mt-lg-5 animate slideIn" style="width:280px;">
                  <div class="fs-5 ms-1 mb-3 text-center fw-semibold">Seja bem-vindo</div>
                  <div class="row g-2 mb-2">
                    <div>
                      <a class="btn btn-secondary py-3 w-100 fs-7 fw-semibold text-white" href="#" role="button">Portal do Inquilino</a>
                    </div>
                    <div>
                      <a class="btn btn-secondary py-3 w-100 fs-7 fw-semibold text-white" href="#" role="button">Portal do Proprietário</a>
                    </div>
                    <div>
                      <a class="btn btn-secondary py-3 w-100 fs-7 fw-semibold text-white" href="http://app.subsee.com.br" target="_blank" role="button">Portal do Corretor</a>
                    </div>
                  </div>
              </div>
            </li>
            <li class="nav-item d-block">
              <button class="p-0 nav-link menu-icon avbar-toggler d-flex flex-column justify-content-between" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="menu-line"></span>
                <span class="menu-line"></span>
                <span class="menu-line"></span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <aside>
    <div class="offcanvas offcanvas-end" data-bs-backdrop="true" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="py-4 px-5 d-block d-md-none">
      <button type="button" class="nav-link" data-bs-dismiss="offcanvas" aria-label="Close" style="transform: rotate(180deg); display: inline-block;"><i class="iconColor icon-back fs-4"></i></button>
      </div>
      
      <div class="offcanvas-body p-0 bg-body-tertiary">
        <div class="justify-content-end flex-grow-5 bg-body px-5 pt-0 pt-md-4 pb-4 border-bottom">
          <a href="/" aria-label="SUB100 Imobiliárias" title="SUB100 Imobiliárias">
            <img class="logo" loading="lazy" src="{{ getImageLogo() }}" alt="{{$config['general']['site_title']}}"/>
          </a>
        </div>
        @if(isset($aguardando))
          <div class="accordion">
            <div class="accordion-item navbar-nav justify-content-end flex-grow-1 p-5 bg-body-tertiary">
              <button class="accordion-button p-0 bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <div class="lh-sm text-dary d-flex gap-2">
                  <div class="d-flex justify-content-center align-items-center rounded-circle border border-4 border-secondary h-100 text-secondary" style="min-width: 50px; min-height: 50px;"> 
                  <i class="icon-user fs-3" title="Usuário" aria-label="Usuário"></i>  
                  </div>
                  <div class="d-flex flex-column justify-content-center">
                    <span class="fs-5">Olá <b class="fw-bold">Walcir</b></span>
                    <span class="fs-7 fw-light">Consumidor</span>
                      @php /* {{ session('menu_opened') ? 'true' : 'false' }} */ @endphp
                      @php /* {{ session('menu_opened') ? 'show' : '' }} */ @endphp
                  </div>
                </div>
              </button>
              <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#profile">
                <div class="accordion-body p-0 mt-3">
                  <ul class="navbar-nav justify-content-end flex-grow-1 p-0 fs-7 fw-light text-day ">
                    <li class="nav-item">
                      <a class="nav-link py-1" href="/imoveis/favoritos">Meus favoritos</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link py-1" href="/visitas-agendadas">Visitas agendadas</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link py-1" href="/propostas-enviadas">Proposta enviadas</a>
                    </li>
                    <li class="nav-item">
                      <button type="button" class="nav-link py-1" id="btnAtualizarDados">Atualizar dados</button>
                    </li>
                    <li class="nav-item">
                      <button type="button" class="nav-link py-1" id="btnAlterarSenha">Alterar senha</button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link fw-bold text-secondary py-1" id="logout" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Sair</button>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        @endif
        <ul class="navbar-nav justify-content-end flex-grow-5 bg-body p-5 pt-4 fs-6 fw-normal text-day">
          <li class="nav-item">
            <a class="nav-link py-1" href="/">Buscar Imóveis</a>
          </li>
          <li class="nav-item">
              <a class="nav-link py-1" href="/imoveis/favoritos">Meus favoritos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-1" href="/anuncie">Quero anunciar</a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-1" href="/institucional/trabalhe-conosco">Trabalhe Conosco</a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-1" href="/institucional">Sobre a SUB100 Imobiliárias</a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-1" href="/institucional/fale-conosco">Fale Conosco</a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-1" href="https://blog.sub100sistemas.com.br/" target="_blank">Blog</a>
          </li>
          @if (isset($aguardando))
          <li class="nav-item">
            <a class="nav-link py-1" href="/institucional/feedback">Feedback</a>
          </li>
          @endif
          <li class="nav-item py-4">
            <div class="btn-group dropup w-100">
              <div class="dropdown-center w-100">
                <button type="button" 
                class="btn btn-secondary py-3 w-100 fs-7 fw-semibold rounded-2" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" aria-label="Quero entrar em contato" title="Quero entrar em contato">
                    Quero entrar em contato
                </button>
                @include('header.partials.contact-dropdown')
              </div>
            </div>
          </li>
          <li class="nav-item pt-2">
            <span class="d-block fw-semibold">Siga nas redes sociais</span>
            <div class="d-flex align-items-center gap-3 my-3">
              <a href="https://www.facebook.com/" target="_blank" class="text-decoration-none">
                <i class="icon-facebook fs-30" title="Facebook" aria-label="Facebook"></i>
              </a>
              <a href="https://www.instagram.com/" target="_blank" class="text-decoration-none">
                <i class="icon-instagram fs-30" title="Instagram" aria-label="Instagram"></i>
              </a>
              <a href="https://www.linkedin.com" target="_blank" class="text-decoration-none">
                <i class="icon-linkedin_2 fs-30" title="Linkedin" aria-label="Linkedin"></i>
              </a>
              <a href="https://x.com" target="_blank" class="text-decoration-none">
                <i class="icon-x fs-30" title="X" aria-label="X"></i>
              </a>
              <a href="https://www.youtube.com/" target="_blank" class="text-decoration-none">
                <i class="icon-youtube fs-30" title="YouTube" aria-label="YouTube"></i>
              </a>
          </div>
          </li>
          <li>
            <hr class="d-none d-md-block my-4">
          </li>
          @foreach ($menu as $item)
          <li class="nav-item">
            <a class="nav-link py-1" href="/pg/{{ $item['url_titulo'] }}">{{$item['titulo']}}</a>
          </li>
          @endforeach
          <li class="nav-item mt-4 fw-light">
            <span class="fs-8">© 2000 - 2025 SUB100 Imobiliárias</span>
          </li>
        </ul>
      </div>
    </div>
  </aside>
  <!-- Modal Atualizar Dados-->
  <div class="modal fade" id="atualizarDados" data-bs-backdrop="true" aria-labelledby="atualizarDadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
        <div class="modal-title fw-bold fs-5" id="atualizarDadosLabel">Atualizar dados</div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Excluir</button>
          <button type="button" class="btn btn-primary">Atualizar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal Alterar Senha-->
  <div class="modal fade" id="alterarSenha" data-bs-backdrop="true" aria-labelledby="alterarSenhaLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
        <div class="modal-title fw-bold fs-5" id="alterarSenhaLabel">Alterar Senha</div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary">Alterar</button>
        </div>
      </div>
    </div>
  </div>
  @push('css')
    <style>
      main {
        visibility: hidden;
      }
    </style>
  @endpush
  @push('js-push')
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        const header = document.querySelector('header');
        const main = document.querySelector('main');

        if (header && main) {
          main.style.paddingTop = header.offsetHeight + 'px';
          main.style.visibility = 'visible';
        }
      });
/*
      document.addEventListener("DOMContentLoaded", () => {
        const btnSenha     = document.getElementById("btnAlterarSenha");
        const btnAd        = document.getElementById("btnAtualizarDados");
        const offcanvasEl  = document.getElementById("offcanvasNavbar");
        const modalSenha   = document.getElementById("alterarSenha");
        const modalAd      = document.getElementById("atualizarDados");

        if (!offcanvasEl || (!btnSenha && !btnAd)) {
          console.error("Elemento necessário não encontrado:", { btnSenha, btnAd, offcanvasEl });
          return;
        }

        if (btnSenha && modalSenha) {
          btnSenha.addEventListener("click", e => {
            e.preventDefault();
            const offInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalSenha);
            offInstance.hide();
            modalInstance.show();
          });
        } else if (btnSenha && !modalSenha) {
          console.error("Modal 'alterarSenha' não encontrado.");
        }

        if (btnAd && modalAd) {
          btnAd.addEventListener("click", e => {
            e.preventDefault();
            const offInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalAd);
            offInstance.hide();
            modalInstance.show();
          });
        } else if (btnAd && !modalAd) {
          console.error("Modal 'atualizarDados' não encontrado.");
        }

        const button = document.querySelector('.accordion-button');
        const collapse = document.querySelector('#collapseOne');
        const isOpened = localStorage.getItem('accordion_opened') === 'true';

        // Aplica estado salvo
        if (isOpened) {
          button.classList.remove('collapsed');
          button.setAttribute('aria-expanded', 'true');
          collapse.classList.add('show');
        } else {
          button.classList.add('collapsed');
          button.setAttribute('aria-expanded', 'false');
          collapse.classList.remove('show');
        }

        // Escuta o clique
        button.addEventListener('click', function () {
          setTimeout(() => {
            const isNowOpened = collapse.classList.contains('show');
            localStorage.setItem('accordion_opened', isNowOpened);
            //console.log("Novo Menu: " + localStorage.getItem('accordion_opened'));
          }, 500); // espera a animação do Bootstrap terminar
        });
        //console.log("Menu: "+localStorage.getItem('accordion_opened'));
      });
      document.getElementById('logout').addEventListener('click', function(event) {
        event.preventDefault(); // Previne o comportamento padrão do link
        // Aqui você pode chamar o método de logout da sua autenticação
        // Exemplo usando Firebase:
        // firebase.auth().signOut().then(() => {
        //   window.location.href = "/"; // Redireciona após logout
        // }).catch((error) => {
        //   console.error("Erro ao sair: ", error);
        // });
        
        // Se o seu sistema for baseado em tokens, talvez você precise limpar o token:
        //localStorage.removeItem('authToken');  // Exemplo de como limpar token localStorage
        //sessionStorage.removeItem('authToken'); // Exemplo de como limpar token sessionStorage

        localStorage.setItem('accordion_opened', false);
        window.location.href = "/";
      });
*/
      //Favorito
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-favorite').forEach(function (btn) {
          const id = btn.dataset.id;
          const icon = btn.querySelector('span');
          const key = 'selected_favorito_' + id;

          // Estado inicial
          if (localStorage.getItem(key)) {
            icon.className = 'icon-favorito_active fs-5 d-block';
            btn.classList.add('active');
          } else {
            icon.className = 'icon-favorito fs-5 d-block';
            btn.classList.remove('active');
          }

          // Clique no botão
          btn.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (icon.classList.contains('icon-favorito')) {
              icon.className = 'icon-favorito_active fs-5 d-block';
              localStorage.setItem(key, true);
              btn.classList.add('active');
            } else {
              icon.className = 'icon-favorito fs-5 d-block';
              localStorage.removeItem(key);
              btn.classList.remove('active');
            }

            atualizarContadorFavoritos();
          });
        });

        atualizarContadorFavoritos();
      });

      window.addEventListener('pageshow', function () {
        atualizarFavoritos();
        atualizarContadorFavoritos();
      });

      // Atualiza os botões (caso volte na navegação)
      function atualizarFavoritos() {
        document.querySelectorAll('.btn-favorite').forEach(function (btn) {
          const id = btn.dataset.id;
          const icon = btn.querySelector('span');
          const key = 'selected_favorito_' + id;

          if (localStorage.getItem(key)) {
            icon.className = 'icon-favorito_active fs-5 d-block';
            btn.classList.add('active');
          } else {
            icon.className = 'icon-favorito fs-5 d-block';
            btn.classList.remove('active');
          }
        });
      }

      // Atualiza contador no topo
      function atualizarContadorFavoritos() {
        const contador = document.getElementById('contador-favoritos');
        if (!contador) return;

        let total = 0;
        for (let i = 0; i < localStorage.length; i++) {
          if (localStorage.key(i).startsWith('selected_favorito_')) {
            total++;
          }
        }

        contador.textContent = total;
        contador.setAttribute('aria-label', `${total} Favoritos`);
      }

    </script>
  @endpush
