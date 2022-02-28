
<link rel="stylesheet" href="../modules/addons/OpenFactura/templates/css/main.css" />
<link rel="stylesheet" href="../modules/addons/OpenFactura/templates/css/links.css" />
<link rel="stylesheet" href="../modules/addons/OpenFactura/templates/css/forms.css" />
<link rel="stylesheet" href="../modules/addons/OpenFactura/templates/css/modal.css" />
<link rel="stylesheet" href="../modules/addons/OpenFactura/templates/css/tinyModal.css" />
<link rel="stylesheet" href="../modules/addons/OpenFactura/templates/css/snackbar.min.css" />
<link rel="stylesheet" href="../modules/addons/OpenFactura/templates/css/snackbar-overrides.css" />
<link rel="stylesheet" href="../modules/addons/OpenFactura/templates/css/data-table-overrides.css" />
<link rel="stylesheet" href="../modules/addons/OpenFactura/templates/css/daterangepicker-overrides.css" />
<script type="text/javascript" src="../modules/addons/OpenFactura/templates/js/tinyModal.min.js"></script>
<script type="text/javascript" src="../modules/addons/OpenFactura/templates/js/snackbar.min.js"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="/js/tinyModal.min.js"></script>
<script type="text/javascript" src="/js/snackbar.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />



<div class="of-whmcs">
  <header>
    <div class="wrapper">
      <div class="wrapper_content">
        <div class="header_flex">
          <a href="{$modulelink}" class="logo"></a>
          <nav>
            <a href="{$modulelink}" class="button icon">
              <i class="ico-settings"></i>
              Configuración
            </a>
            <a href="{$modulelink}&action=registration" class="button active icon">
              <i class="ico-register"></i>
              Registro de autoservicios
            </a>
          </nav>
        </div>
      </div>
    </div>
  </header>


  <div class="wrapper">
    <div class="wrapper_content">
      <h1>Registro de autoservicio</h1>
      <p>
        Cada invoice de WHMCS que se marque como pagado generará un enlace de autoservicio que se envía automáticamente
        al
        cliente. Desde este registro, podrás reenviar ese enlace al cliente o acceder a él. El enlace no requiere
        autenticación y cualquier persona que lo tenga podrá generar la boleta o factura electrónica asociada a la
        compra,
        o bien descargarla en caso de que ya haya sido generada.
      </p>
      
      <div class="linksBlock">
        <a href="#manual-invoice" class="tinymodal-modal link-blue">Agregar Invoice manualmente</a>
        <button id="daterange" name="daterange" class="link-blue">Exportar CSV</button>
      </div>
    </div>

    <div id="manual-invoice" class="tinymodal-window ofModal">
      <div class="tinymodal-inner">
        <h2 class="ofModal__header">Ingrese el número de orden de compra</h2>
        <div class="ofModal__body">
            <div class="form-field form-field__pure temp-form-field--has-error">
              <div class="form-field__control">
                <input
                  type="text"
                  name="invoiceid"
                  id="invoiceid"
                  placeholder="Busqueda por ID"
                  required
                  class="form-field__input">
              </div>
              <div class="form-field__errors">
                  <div id='error-message' ></div>
              </div>
            </div>
        </div>
    
        <div class="ofModal__actions">
          <a href="" class="of-btn of-btn-primary of-btn-upcase js-tiny-close">Cancelar</a>
          <button id="send-invoice" class="of-btn of-btn-primary of-btn-upcase" tabindex="1">Agregar</button>
        </div>
      </div>
    </div>


<script>
    var today = new Date();
    var dd = today.getDate();
  $('#daterange').daterangepicker(
    {
      opens: 'center',
      startDate: '01/01/2019', 
      endDate: Date.now(),
      maxDate: today,
      locale: {
        format: 'DD/MM/YYYY',
        applyLabel: 'Aplicar',
        cancelLabel: 'Cancelar',
        "daysOfWeek": [
          "D",
          "L",
          "M",
          "M",
          "J",
          "V",
          "S"
        ],
      }
    },
    function(start, end, label) {
      window.location.href = "addonmodules.php?module=OpenFactura&action=exportCSV&start="+start.format('YYYY-MM-DD')+"&end="+end.format('YYYY-MM-DD');
    }
  );
  $('#daterange').on('apply.daterangepicker', function(ev, picker) {
    window.location.href = "addonmodules.php?module=OpenFactura&action=exportCSV&start="+picker.startDate.format('YYYY-MM-DD')+"&end="+picker.endDate.format('YYYY-MM-DD');
  });
</script>


    <section class="has-table">
      <div class="OF-dataTable__wrapper">
        <div class="progressBar">
          <div class="indeterminate"></div>
        </div>
        <table id="dataTable" class="OF-dataTable hover" style="width: 100%;"></table>
      </div>
    </section>
  </div>
</div>
<script src="/templates/js/main.js"></script>

<script>
  $(document).ready(function() {
    const links = document.querySelectorAll('a.tinymodal-modal');
    
    for (var i = 0; links.length > i; i++) {
      links[i].addEventListener("click", function (event) {
        event.preventDefault();
        const element = this.getAttribute("href");
        tinyModal.openModal(element, function () {
          document.getElementsByTagName('body')[0].classList.add('no-scroll');
        });
      });
    }
    
    const closeLinks = document.querySelectorAll('.js-tiny-close');

    [].forEach.call(closeLinks, function(link) {
      link.addEventListener('click', function(event) {
        event.preventDefault();
        const isShouldSubmitForm = link.classList.contains('js-submit');
        closeModal(isShouldSubmitForm);
      });
    });
  });

  $('body').on('click', function(event) {
    if ( !$('body').hasClass('tinymodal-active') ) {
      document.getElementsByTagName('body')[0].classList.remove('no-scroll');
      let error=document.getElementById('error-message');
      error.innerText='';
      let invoice=document.getElementById('invoiceid');
      invoice.value='';
    }
  })

  function closeModal(isShouldSubmit) {
    tinyModal.closeModal(function(){
      if (isShouldSubmit) {
        
      }

      clearForm(this);

      document.getElementsByTagName('body')[0].classList.remove('no-scroll');
    });
  }

  function clearForm(htmlContainer) {
    const inputs = htmlContainer.querySelectorAll('input[type=text]');
    [].forEach.call(inputs, function(input) {
      input.value = '';
    });
  }
</script>

<script>
var input = document.getElementById("invoiceid");
input.addEventListener("keyup", function(event) {
  if (event.keyCode === 13) {
   event.preventDefault();
   document.getElementById("send-invoice").click();
  }
});
</script>


