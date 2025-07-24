<header class="position-fixed top-0 w-100 z-6 shadow-sm">
    <nav class="navbar py-4 px-2 px-md-0">
      <div class="container">
        <div class="col">
          <a href="/" aria-label="SUB100 Imobiliárias" title="SUB100 Imobiliárias">
            <img class="{{ getSvgLogo('css','main') }} {{ strtolower(getColorTopFont()) == '#ffffff' ? 'headerLogo' : '' }}"
                loading="eager" 
                fetchpriority="high"
                src="{{ getSvgLogo('src','main') }}"
                alt="{{$config['general']['site_title']}}"
                />
          </a>
        </div>
        <div class="col-auto">
          <ul class="d-flex align-items-center gap-3 mb-0 dropdown-center">
            @if(!empty($config['phone'])) 
            <li class="nav-item d-none d-md-block"> 
              <button type="button" class="nav-link" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" aria-label="WhatsApp">
                <i class="icon-whatsapp fs-30" title="Contato" aria-label="Contato"></i>
              </button>
              @include('header.partials.contact-dropdown')
            </li>
            @endif
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
      <div class="offcanvas-body p-0 bg-body-tertiary">
        <div class="bg-body px-5 py-4 border-bottom d-flex align-items-center justify-content-between">
          <img class="{{ getSvgLogo('css','main') }} offcanvasLogo" 
              loading="eager" 
              fetchpriority="high"
              src="{{ getSvgLogo('src','main') }}" 
              alt="{{$config['general']['site_title']}}"
              />
          <button type="button" class="btn-close nav-link text-primary fw-bold" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
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
            <a class="nav-link py-1" href="/institucional">Sobre a {{ getSiteName() }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-1" href="/institucional/fale-conosco">Fale Conosco</a>
          </li>
          @if(!empty(hasModules('articles')))
          <li class="nav-item">
            <a class="nav-link py-1" href="https://blog.sub100sistemas.com.br/" target="_blank">Blog</a>
          </li>
          @endif
          @if (isset($aguardando))
          <li class="nav-item">
            <a class="nav-link py-1" href="/institucional/feedback">Feedback</a>
          </li>
          @endif
          @if(!empty($config['phone'])) 
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
          @else
          <li class="nav-item py-4">
            <hr class="d-none d-md-block my-3">
          </li>
          @endif
          @if(!empty(getSocialNetwork())) 
          <li class="nav-item pt-2">
            <span class="d-block fw-semibold">Siga nas redes sociais</span>
            <div class="d-flex align-items-center gap-3 my-3">
              @foreach(getSocialNetwork() as $item)
                <a href="{{$item['url']}}" target="_blank" class="text-decoration-none">
                    <i class="{{ 'icon-'.$item['name']}} fs-30" title="{{ ucfirst($item['name']) }}" aria-label="{{ ucfirst($item['name']) }}"></i>
                </a>
              @endforeach
            </div>
          </li>
          @endif
          <li>
            <hr class="d-none d-md-block my-4">
          </li>
          @if(isset($user))
          @foreach ($menu as $item)
          <li class="nav-item">
            <a class="nav-link py-1" href="/pg/{{ $item['url_titulo'] }}">{{$item['titulo']}}</a>
          </li>
          @endforeach
          @endif
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