
<link rel="stylesheet" href="{$RootDirectory}/modules/addons/OpenFactura/templates/css/main.css" />

<div class="of-whmcs">
  <header>
    <div class="wrapper">
      <div class="wrapper_content">
        <div class="header_flex">
          <a href="{$modulelink}" class="logo"></a>
          <nav>
            <a href="{$modulelink}" class="button active icon">
              <i class="ico-settings"></i>
              Configuración
            </a>
            <a href="{$modulelink}&action=registration" class="button icon">
              <i class="ico-register"></i>
              Registro de autoservicio
            </a>
          </nav>
        </div>
      </div>
    </div>
  </header>

  <div class="wrapper">
    <div class="wrapper_content">
      <h1>Configuración</h1>
      <p>
        La Integración de OpenFactura enviará un correo al cliente para que pueda generar su propio documento electrónico,
        ya sea boleta o factura, a través del Autoservicio de Emisión, ingresando únicamente los datos de receptor.
        Este correo se envía al momento de darse por pagado el 'Invoice' (InvoicePaid = True).
        Si tienes alguna duda acerca de los campos del Invoice que se utilizan para generar el documento,
        puedes revisar nuestra Documentación de Integración con WHMCS
        {* <a href="#" class="linkBlue"> </a> *}
      </p>
    </div>
    <form action="" onsubmit="return sendForm(event)" id="form1">
      <section>
        <h2>Opciones generales</h2>
        <div class="checkBoxContainer">
          <div class="md-checkboxWrapper">
            <input type="checkbox" id="check1" name="automatic39" value="1" {if !empty($valueAutomatic39) && $valueAutomatic39 == 1}checked="checked"{/if}/>
            <label for="check1" class="md-checkbox"></label>
          </div>

          <div>
            <label for="check1" class="checkLabel">Habilitar emisión y envío automático de boletas</label>
            <div>
              Al seleccionar esta opción, una boleta electrónica se emitirá por
              defecto y será adjuntada al correo que se enviará al cliente.
            </div>
          </div>
        </div>

        <div class="checkBoxContainer">
          <div class="md-checkboxWrapper">
            <input type="checkbox" id="check2" name="allow33" value="1" {if !empty($valueAllow33) && $valueAllow33 == 1}checked="checked"{/if}/>
            <label for="check2" class="md-checkbox"></label>
          </div>

          <div>
            <label for="check2" class="checkLabel">Permitir al cliente ingresar datos de receptor para generar su
              factura</label>
            <div>
              Se le permitirá al cliente la posibilidad de emitir su propia factura electrónica o bien convertir una boleta
              a factura electrónica, según sea el caso, ingresando sus datos de facturación. Se generará una Nota de
              Crédito.
            </div>
          </div>
        </div>

        <div class="checkBoxContainer">
          <div class="md-checkboxWrapper">
            <input type="checkbox" id="check3" name="enableLogo" value="1" {if !empty($valueEnableLogo) && $valueEnableLogo == 1}checked="checked"{/if}/>
            <label for="check3" class="md-checkbox"></label>
          </div>

          <div>
            <label for="check3" class="checkLabel">Habilitar logotipo personalizado</label>
            <div>
              El enlace de autoservicio que se enviará al cliente podrá ir con
              un logotipo personalizado de la empresa.
              <a href="#" class="linkBlue _openDialog-preview">Ver ejemplo.</a>
            </div>

            <div class="form-field">
              <div class="form-field__control">
                <label for="logo-url" class="form-field__label">URL logo empresa</label>
                <input id="logo-url"
                      name="logo-url"
                      type="text"
                      class="form-field__input"
                      value="{$urlLogo}">
              </div>
              <div class="form-field__hint">
                No se mostrará el logotipo si la URL no es https. Proporciones 16:9, Dimensiones ideales de 128 X 72px.
              </div>
            </div>
          </div>
        </div>
      </section>
      <section>
        <div class="progressBar">
          <div class="indeterminate"></div>
        </div>
        <div class="flex-menu">
          <h2>Información del emisor</h2>
          <button id="update-button" class="button-flat">Actualizar</button>
        </div>
        <p>
          Los siguientes campos se obtienen desde el SII, a través de
          OpenFactura, y no pueden ser modificados desde acá. Si cuentas con
          sucursales, puedes seleccionar la que desees ocupar. Si has realizado
          cambios en el SII, recuerda hacer clic en 'Actualizar' para que se
          vean reflejados.
        </p>

          <div class="s-row">
            <div class="col-2">
              <div class="form-field">
                <div class="form-field__control">
                  <label for="rut" class="form-field__label">RUT</label>
                  <input id="rut" type="tex t" class="form-field__input" value="{$rutEmisor}" disabled>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-field">
                <div class="form-field__control">
                  <label for="company-name" class="form-field__label">Razón Social</label>
                  <input id="company-name" type="text" class="form-field__input" value="{$rznSoc}" disabled>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-field">
                <div class="form-field__control">
                  <label for="description" class="form-field__label">Glosa descriptiva (Ex Giro)</label>
                  <input id="description" type="text" class="form-field__input" value="{$glosa}" disabled>
                </div>
              </div>
            </div>
          </div>

        <div class="s-row">
          <div class="col-2">
            <div class="form-field">
              <div class="form-field__select">

                <div class="form__group">
                  <label for="branch" class="form-field__label">Sucursal</label>
                    <div class="form__dropdown">
                    <select name="branch" id="branch">
                      {html_options values=$sucursales_key output=$sucursales_value selected=$sucursales_active}
                    </select>
                  </div>
                </div>

              </div>
            </div>
          </div>
          <div class="col-3">
            <div class="form-field">
              <div class="form-field__control">
                <label for="activity" class="form-field__label">Actividad económica</label>
                <input id="activity" type="text" class="form-field__input" value="{$acteco}" disabled>
              </div>
            </div>
          </div>
        </div>
      </section>

      <div class="wrapper_content">
        <button type="submit" class="button-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>


<script src="{$RootDirectory}/modules/addons/OpenFactura/templates/js/main.js"></script>


