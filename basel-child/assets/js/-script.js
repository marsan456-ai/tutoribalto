jQuery( document ).ready(function(){
    jQuery('#billing_country').change(function(){ if(jQuery(this).val()=='GB'){  
        jQuery('.uk_open').trigger('click');
       // var txts="<ul class='woocommerce-error' role='alert'><li>Dear customer, to purchase our Balto braces in your Country please contact our UK dealer:<br><br>KVP EU Limited<br>Unit 21 Horn Park Business Estate<br>Broadwindsor Road – Beaminster – Dorset<br>DT8 3PT – United Kingdom<br><br>Phone: +44(0) 1308 867020<br>email: info@kvpeu.com<br><br>Thanks<br></li></ul>" 
     /// jQuery("#cerror").html('');  jQuery("#cerror").html(txts);
     //  jQuery('html, body').animate({
      //        scrollTop: jQuery("#content").offset().top
        //  }, 2000);
      }else if(jQuery(this).val()=='FR'){ 
        jQuery('.france_open').trigger('click');
          // var txts="<ul class='woocommerce-error' role='alert'><li>Dear customer, you can purchase our Balto braces even in your Country.<br>Please contact our France dealer:<br><br>MIKAN<br><br>Usine Créative, parc Vendée Sud Loire 1<br>85600 Boufféré - France<br>phone: +33 (0)2 51 62 15 73<br>website: www.mikan-vet.com<br>email: info@mikan-vet.com<br><br>Thanks.<br><br><br></li></ul>" 
     //  jQuery("#cerror").html('');   jQuery("#cerror").html(txts);
      //  jQuery('html, body').animate({
      //        scrollTop: jQuery("#content").offset().top
       //   }, 2000);
      }else if(jQuery(this).val()=='DE'){
        
        jQuery('.german_open').trigger('click');
      }else if(jQuery(this).val()=='US'){
        
        jQuery('.usa_open').trigger('click');
      }else{
      jQuery("#cerror").html(''); 
      } });

      jQuery('.tab-label').click(function(){
        setInterval(function(){ 
        if (the_ajax_script.current_language == "en") {
            jQuery('.from').html('from');
        }else if (the_ajax_script.current_language == "de") {
            jQuery('.from').html('ab');
        }else if (the_ajax_script.current_language == "fr") {
            jQuery('.from').html('à partir de');
        }else if (the_ajax_script.current_language == "es") {
            jQuery('.from').html('desde'); 
        }
    }, 500);
        });
      if (the_ajax_script.current_language == "en") {
        jQuery('.from').html('from');
        jQuery('#pa_zampa > option:first').text("Choose the paw ");  
        jQuery('#pa_zampa').after("<br>looking at the dog from behind");  
        jQuery("label[for='billing_first_name']").html("First name&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_last_name']").html("Last name&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='reg_email']").html("Email address&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_email']").html("Email address&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_phone']").html("Phone&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_tel']").html("Phone&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_country']").html("Country&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_address_1']").html("Street address&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_address_1').attr("placeholder", "Street address");

        jQuery("label[for='billing_verify_email']").html("Confirm e-mail&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_verify_email').attr("placeholder", "Confirm e-mail");

        jQuery("label[for='billing_city']").html("Town / City&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_state']").html("Province&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_postcode']").html("Postcode / ZIP&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_client_type_corp']").html("Customer type&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery(".woocommerce-input-wrapper label[for='billing_client_type_corp']").html("Company");

        jQuery("label[for='billing_fiscal']").html("Fiscal Code&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_rs']").html("Company name&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_rs').attr("placeholder", "Company name");
        jQuery("label[for='billing_piva']").html("VAT number&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_piva').attr("placeholder", "VAT number");
        jQuery("label[for='billing_codice_sdi']").html("SDI code&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_codice_sdi').attr("placeholder", "SDI code");
        jQuery("label[for='billing_company']").html("Company name");
       
    }else if (the_ajax_script.current_language == "de") {
        jQuery('.from').html('ab');
        jQuery('#pa_zampa > option:first').text("Wähle die Pfote ");
        jQuery('#pa_zampa').after("<br>schaue den Hund von hinten an");  
        jQuery("label[for='billing_first_name']").html("Vorname&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_last_name']").html("Nachname&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='reg_email']").html("E-Mail-Adresse&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_email']").html("E-Mail-Adresse&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_phone']").html("Telefon&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_tel']").html("Telefon&nbsp;<abbr class='required' title='required'>*</abbr>");
      
        jQuery("label[for='billing_verify_email']").html("E-Mail bestätigen&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_verify_email').attr("placeholder", "E-Mail bestätigen");

        jQuery("label[for='billing_country']").html("Land&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_address_1']").html("Straße&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_address_1').attr("placeholder", "Straße");
        jQuery("label[for='billing_city']").html("Ort / Stadt&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_state']").html("Provinz&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_postcode']").html("Postleitzahl&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_client_type_corp']").html("Kundenty&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery(".woocommerce-input-wrapper label[for='billing_client_type_corp']").html("Unternehmen");

        jQuery("label[for='billing_fiscal']").html("Fiscal Code&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_rs']").html("Firmenname&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_rs').attr("placeholder", "Firmenname");
        jQuery("label[for='billing_piva']").html("Umsatzsteuernummer&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_piva').attr("placeholder", "Umsatzsteuernummer");
        jQuery("label[for='billing_codice_sdi']").html("SDI code&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_codice_sdi').attr("placeholder", "SDI code");
        jQuery("label[for='billing_company']").html("Firmenname");
    }else if (the_ajax_script.current_language == "fr") {
        jQuery('.from').html('à partir de');
        jQuery('#pa_zampa > option:first').text("Choisissez la patte ");
        jQuery('#pa_zampa').after("<br>regardant le chien par derrière"); 
        jQuery("label[for='billing_first_name']").html("Prénom&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_last_name']").html("Nom&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='reg_email']").html("Adresse de messagerie&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_email']").html("Adresse de messagerie&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_phone']").html("Téléphone&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_tel']").html("Téléphone&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_verify_email']").html("Confirmez votre email&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_verify_email').attr("placeholder", "Confirmez votre email");
        jQuery("label[for='billing_country']").html("Pays&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_address_1']").html("Numéro et nom de rue&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_address_1').attr("placeholder", "Numéro et nom de rue");
        jQuery("label[for='billing_city']").html("Ville&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_state']").html("Province&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_postcode']").html("Code postal&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_client_type_corp']").html("Type de client&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery(".woocommerce-input-wrapper label[for='billing_client_type_corp']").html("Société");

        jQuery("label[for='billing_fiscal']").html("Fiscal Code&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_rs']").html("Nom de l'entreprise&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_rs').attr("placeholder", "Nom de l'entreprise");
        jQuery("label[for='billing_piva']").html("Numéro de TVA&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_piva').attr("placeholder", "Numéro de TVA");
        jQuery("label[for='billing_codice_sdi']").html("SDI code&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_codice_sdi').attr("placeholder", "SDI code");
        jQuery("label[for='billing_company']").html("Nom de l'entreprise");
    }else if (the_ajax_script.current_language == "es") {
        jQuery('.from').html('desde');   
        jQuery('#pa_zampa > option:first').text("Elige la pata ");   
        jQuery('#pa_zampa').after("<br>mirando al perro desde atrás");   
        jQuery("label[for='billing_first_name']").html("Nombre&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_last_name']").html("Apellidos&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='reg_email']").html("Correo electrónico&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_email']").html("Correo electrónico&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_phone']").html("Teléfono&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_tel']").html("Teléfono&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_verify_email']").html("Confirmar el correo&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_verify_email').attr("placeholder", "Confirmar el correo");
        jQuery("label[for='billing_country']").html("País&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_address_1']").html("Dirección de la calle &nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_address_1').attr("placeholder", "Dirección de la calle ");
        jQuery("label[for='billing_city']").html("Pueblo / Ciudad&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_state']").html("Provincia&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_postcode']").html("Código postal&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_client_type_corp']").html("Tipo de cliente&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery(".woocommerce-input-wrapper label[for='billing_client_type_corp']").html("Empresa");
        jQuery("label[for='billing_fiscal']").html("Fiscal Code&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery("label[for='billing_rs']").html("Nombre de la compania&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_rs').attr("placeholder", "Nombre de la compania");
        jQuery("label[for='billing_piva']").html("Numero de iva&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_piva').attr("placeholder", "Numero de iva");
        jQuery("label[for='billing_codice_sdi']").html("SDI code&nbsp;<abbr class='required' title='required'>*</abbr>");
        jQuery('#billing_codice_sdi').attr("placeholder", "SDI code");
        jQuery("label[for='billing_company']").html("Nombre de la compania");
    }else if (the_ajax_script.current_language == "it") {
         
        jQuery('#pa_zampa > option:first').text("Scegli la zampa"); 
        jQuery('#pa_zampa').after("<br>guardando il cane da dietro");        
    }

    jQuery(".whole-sale-register").submit(function(event) {
        var reg_username=jQuery('#reg_username').val();
        var reg_email=jQuery('#reg_email').val();
        var reg_password=jQuery('#reg_password').val();
        if(reg_username==''){
            jQuery('#reg_username').css("border", "1px solid red");
        }else{
            jQuery('#reg_username').css("border", "1px solid rgba(129,129,129,.25)");
        }
        if(reg_email==''){
            jQuery('#reg_email').css("border", "1px solid red");
        }else{
            jQuery('#reg_email').css("border", "1px solid rgba(129,129,129,.25)");
        }
        if(reg_password==''){
            jQuery('#reg_password').css("border", "1px solid red");
        }else{
            jQuery('#reg_password').css("border", "1px solid rgba(129,129,129,.25)");
        }
        if(reg_username!='' && reg_email!='' && reg_password!=''){
            jQuery.ajax({
                type : "post",
                dataType: 'json',
                url : the_ajax_script.ajax_url,
                data : {action: "wholesale_registers", reg_username : reg_username,reg_email : reg_email,reg_password : reg_password},
                beforeSend: function(){
                
               },
                success: function(response) {
                 
       
                }
             });


        }

        return false;
    });   

    jQuery("#billing_piva").before("<span class='required_piva' style = \"color: red;\">*</span>");
    jQuery("#billing_pec").before("<span class='required_piva' style = \"color: red;\">*</span>");
    jQuery("#billing_codice_sdi").before("<span class='required_piva' style = \"color: red;\">*</span>");
    jQuery("#billing_rs").before("<span class='required_rs' style = \"color: red;\">*</span>");
    jQuery("#billing_fiscal").before("<span class='required_fiscal' style = \"color: red;\">*</span>");
    jQuery('select#billing_country, select#shipping_country').on( 'change', function (){
        setTimeout(function(){ jQuery('#billing_client_type_private').trigger('click'); }, 500);
setTimeout(function(){ jQuery('#billing_client_type_corp').trigger('click'); }, 700);
setTimeout(function(){ jQuery('#billing_client_type_private').trigger('click'); }, 1000);
    });
    setTimeout(function(){ jQuery('#billing_client_type_private').trigger('click'); }, 500);
setTimeout(function(){ jQuery('#billing_client_type_corp').trigger('click'); }, 700);
setTimeout(function(){ jQuery('#billing_client_type_private').trigger('click'); }, 1000);
});
jQuery( document ).on( 'updated_checkout', function() {
    setTimeout(function(){ jQuery('#billing_client_type_private').trigger('click'); }, 500);
    setTimeout(function(){ jQuery('#billing_client_type_corp').trigger('click'); }, 700);
    setTimeout(function(){ jQuery('#billing_client_type_private').trigger('click'); }, 1000);
    setInterval(function(){ 

        if (the_ajax_script.current_language == "en") {
            jQuery("label[for='billing_client_type_corp']").text('Company');
            jQuery("label[for='billing_client_type_private']").text('Private');
            jQuery('#billing_client_type_field > label').text('Customer type');
            jQuery("label[for='billing_rs']").text('Company name');
            jQuery("#billing_rs").attr('placeholder',"Company name");
            jQuery("label[for='billing_piva']").text('VAT number');
            jQuery("#billing_piva").attr('placeholder','VAT number');
            jQuery("label[for='billing_codice_sdi']").text('Codice SDI');
            jQuery("label[for='billing_pec']").text('P.E.C.');
            jQuery("label[for='billing_fiscal_repeat']").text('Repeat fiscal code');
            jQuery("#billing_fiscal_repeat").attr('placeholder',"Repeat fiscal code");
        }else if (the_ajax_script.current_language == "de") {
            jQuery("label[for='billing_client_type_corp']").text('Unternehmen');
            jQuery("label[for='billing_client_type_private']").text('Privatgelände');
            jQuery('#billing_client_type_field > label').text('Kundenty');
            jQuery("label[for='billing_rs']").text('Firmenname');
            jQuery("#billing_rs").attr('placeholder',"Firmenname");
            jQuery("label[for='billing_piva']").text('Umsatzsteuernummer');
            jQuery("#billing_piva").attr('placeholder','Umsatzsteuernummer');
            jQuery("label[for='billing_codice_sdi']").text('Codice SDI');
            jQuery("label[for='billing_pec']").text('P.E.C.');
            jQuery("label[for='billing_fiscal_repeat']").text('Repeat fiscal code');
            jQuery("#billing_fiscal_repeat").attr('placeholder',"Repeat fiscal code");
        }else if (the_ajax_script.current_language == "fr") {
            jQuery("label[for='billing_client_type_corp']").text('Société');
            jQuery("label[for='billing_client_type_private']").text('Privé');
            jQuery('#billing_client_type_field > label').text('Type de client');
            jQuery("label[for='billing_rs']").text("Nom de l'entreprise");
            jQuery("#billing_rs").attr('placeholder',"Nom de l'entreprise");
            jQuery("label[for='billing_piva']").text('Numéro de TVA');
            jQuery("#billing_piva").attr('placeholder','Numéro de TVA');
            jQuery("label[for='billing_codice_sdi']").text('Codice SDI');
            jQuery("label[for='billing_pec']").text('P.E.C.');
            jQuery("label[for='billing_fiscal_repeat']").text('Repeat fiscal code');
            jQuery("#billing_fiscal_repeat").attr('placeholder',"Repeat fiscal code");
        }else if (the_ajax_script.current_language == "es") {
            jQuery("label[for='billing_client_type_corp']").text('Empresa');
            jQuery("label[for='billing_client_type_private']").text('Privado');
            jQuery('#billing_client_type_field > label').text('Tipo de cliente');
            jQuery("label[for='billing_rs']").text("Numero de la compania");
            jQuery("#billing_rs").attr('placeholder','Numero de la compania');
            jQuery("label[for='billing_piva']").text('Numero de iva');
            jQuery("#billing_piva").attr('placeholder','Numero de iva');
            jQuery("label[for='billing_codice_sdi']").text('Codice SDI');
            jQuery("label[for='billing_pec']").text('P.E.C.');
            jQuery("label[for='billing_fiscal_repeat']").text('Repeat fiscal code');
            jQuery("#billing_fiscal_repeat").attr('placeholder',"Repeat fiscal code");
        }
        if(jQuery('input[name="billing_client_type"]:checked').val()=="private"){
            jQuery("#billing_rs_field").hide();
            jQuery("#billing_piva_field").hide();
            jQuery("#billing_codice_sdi_field").hide();
            jQuery("#billing_pec_field").hide();
        }
        if(jQuery('input[name="billing_client_type"]:checked').val()=="corp" && (jQuery("#select2-billing_country-container").text()=='Italie' || jQuery("#select2-billing_country-container").text()=='Italy' || jQuery("#select2-billing_country-container").text()=='Italia' || jQuery("#select2-billing_country-container").text()=='Italien')){
            jQuery("#billing_codice_sdi_field").show();
            jQuery("#billing_pec_field").show();  
        }else{
            jQuery("#billing_codice_sdi_field").hide();
            jQuery("#billing_pec_field").hide();  
            jQuery("#billing_codice_sdi_field").hide();
            jQuery("#billing_pec_field").hide();
        }
     }, 1000);

     
    jQuery('input[type="radio"]').click(function(){
        if(jQuery(this).attr("value")=="private"){
            jQuery("#billing_rs_field").hide();
            jQuery("#billing_piva_field").hide();
            jQuery("#billing_codice_sdi_field").hide();
            jQuery("#billing_pec_field").hide();
            jQuery("#billing_rs").prop('required',false);
            jQuery("#billing_piva").prop('required',false);
            jQuery("#billing_fiscal").prop('required',false);

            if (jQuery("#select2-billing_country-container").text()=='Italy' || jQuery("#select2-billing_country-container").text()=='Italia' || jQuery("#select2-billing_country-container").text()=='Italien')
            {
                jQuery(".required_fiscal").show();
                jQuery("#billing_fiscal").prop('required',true);
                jQuery("#billing_fiscal").attr('maxlength', 16);

                                            

            }
            else 
            {
                jQuery("#billing_fiscal").prop('required',false);
                jQuery(".required_fiscal").hide();
            }

            
        }
        if(jQuery(this).attr("value")=="corp"){

            jQuery("#billing_rs_field").show();
            jQuery("#billing_piva_field").show();
            
            jQuery("#billing_fiscal").attr('placeholder','Codice fiscale');
            jQuery("#billing_fiscal").attr('maxlength', 100);
            jQuery("#billing_rs").attr('placeholder','Ragione Sociale (obbligatorio)');
            jQuery("#billing_piva").attr('placeholder','Partita IVA (obbligatorio)');

            if (jQuery("#select2-billing_country-container").text()=='Italie' || jQuery("#select2-billing_country-container").text()=='Italy' || jQuery("#select2-billing_country-container").text()=='Italia' || jQuery("#select2-billing_country-container").text()=='Italien') 
            {
                jQuery("#billing_fiscal_field").show();
                jQuery("#billing_fiscal_repeat_field").show();
                jQuery("#billing_fiscal").prop('required',false);
                jQuery("#billing_piva").prop('required',true);
                jQuery("#billing_rs").prop('required',true);
                jQuery(".required_fiscal").hide();
                jQuery(".required_piva").show();
                jQuery(".required_rs").show();
                jQuery("#billing_codice_sdi_field").show();
            jQuery("#billing_pec_field").show();
                if (the_ajax_script.current_language == "en") {
                   jQuery("#billing_fiscal").attr('placeholder','Fiscal code (not required)');
                jQuery("label[for='billing_fiscal']").text("Fiscal code (not required)"); 
                jQuery("#billing_codice_sdi_field").hide();
            jQuery("#billing_pec_field").hide();
                 }else if (the_ajax_script.current_language == "it") {
                   jQuery("#billing_fiscal").attr('placeholder','Codice fiscale');
                jQuery("label[for='billing_fiscal']").text("Codice fiscale"); 
                 }else if (the_ajax_script.current_language == "de") { 
                   jQuery("label[for='billing_client_type_private']").text("Privatgelände");
                   jQuery("label[for='billing_client_type_corp']").text("Unternehmen");
                  jQuery("#billing_rs").attr('placeholder','Firmenname');
                jQuery("label[for='billing_rs']").text("Firmenname"); 
                jQuery("#billing_piva").attr('placeholder','Umsatzsteuernummer');
                jQuery("label[for='billing_piva']").text("Umsatzsteuernummer"); 
                   jQuery("#billing_fiscal").attr('placeholder','Steuerkennzeichen (nicht erforderlich)');
                jQuery("label[for='billing_fiscal']").text("Steuerkennzeichen (nicht erforderlich)"); 
                jQuery("#billing_codice_sdi_field").hide();
                jQuery("#billing_pec_field").hide();
                 }

            }

            else 
            {
                jQuery("#billing_fiscal_field").hide();
                jQuery("#billing_fiscal_repeat_field").hide();
                jQuery("#billing_fiscal").prop('required',false);
                jQuery("#billing_piva").prop('required',false);
                jQuery("#billing_rs").prop('required',false);
                jQuery(".required_fiscal").hide();
                jQuery(".required_piva").hide();
                jQuery(".required_rs").hide();
                if (the_ajax_script.current_language == "en") {
                   jQuery("#billing_fiscal_field").attr('placeholder','fiscal code (not required)');
                jQuery("label[for='billing_fiscal']").text("Fiscal Code (not required)"); 
                 }else if (the_ajax_script.current_language == "it") {
                   jQuery("#billing_fiscal_field").attr('placeholder','Codice fiscale');
                jQuery("label[for='billing_fiscal']").text("Codice fiscale"); 
                 }else if (the_ajax_script.current_language == "de") { 
                  jQuery("#billing_rs").attr('placeholder','Firmenname');
                jQuery("label[for='billing_rs']").text("Firmenname"); 
                jQuery("#billing_piva").attr('placeholder','Umsatzsteuernummer');
                jQuery("label[for='billing_piva']").text("Umsatzsteuernummer"); 
                   jQuery("#billing_fiscal_field").attr('placeholder','Steuerkennzeichen (nicht erforderlich)');
                jQuery("label[for='billing_fiscal']").text("Steuerkennzeichen (nicht erforderlich)"); 
                 }
            }


        }else{
           if (the_ajax_script.current_language == "en") {
                   jQuery("#billing_fiscal_field").attr('placeholder','fiscal code');
                jQuery("label[for='billing_fiscal']").text("Fiscal Code"); 
                 }else if (the_ajax_script.current_language == "it") {
                   jQuery("#billing_fiscal_field").attr('placeholder','Codice fiscale');
                jQuery("label[for='billing_fiscal']").text("Codice fiscale"); 
                 }else if (the_ajax_script.current_language == "de") { 
                   jQuery("#billing_fiscal").attr('placeholder','Steuerkennzeichen');
                jQuery("label[for='billing_fiscal']").text("Steuerkennzeichen"); 
                 }
        }

    });
   
 
});
