/**
 * Checkout JavaScript
 *
 * @package Tutoribalto_Theme
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		TutoribaltoCheckout.init();
	});

	window.TutoribaltoCheckout = {

		/**
		 * Initialize checkout
		 */
		init: function() {
			this.clientTypeToggle();
			this.countryChangeHandler();
			this.fiscalCodeValidation();
			this.emailVerification();
			this.translateLabels();
		},

		/**
		 * Handle client type toggle (private/company)
		 */
		clientTypeToggle: function() {
			const self = this;

			// Initial state
			self.updateFieldsVisibility();

			// On change
			$('input[name="billing_client_type"]').on('change', function() {
				self.updateFieldsVisibility();
			});

			// Trigger on checkout update
			$(document.body).on('updated_checkout', function() {
				self.updateFieldsVisibility();
				self.translateLabels();
			});
		},

		/**
		 * Update field visibility based on client type and country
		 */
		updateFieldsVisibility: function() {
			const clientType = $('input[name="billing_client_type"]:checked').val();
			const country = $('#billing_country').val();

			if (clientType === 'private') {
				// Hide company fields
				$('#billing_rs_field, #billing_piva_field, #billing_codice_sdi_field, #billing_pec_field').hide();
				$('#billing_rs, #billing_piva').prop('required', false);

				// Show fiscal code only for Italy
				if (country === 'IT') {
					$('#billing_fiscal_field, #billing_fiscal_repeat_field').show();
					$('#billing_fiscal').prop('required', true);
				} else {
					$('#billing_fiscal_field, #billing_fiscal_repeat_field').hide();
					$('#billing_fiscal').prop('required', false);
				}
			} else if (clientType === 'corp') {
				// Show company fields
				$('#billing_rs_field, #billing_piva_field').show();
				$('#billing_rs, #billing_piva').prop('required', true);

				// Show SDI and PEC only for Italy
				if (country === 'IT') {
					$('#billing_codice_sdi_field, #billing_pec_field').show();
					$('#billing_fiscal_field, #billing_fiscal_repeat_field').show();
					$('#billing_fiscal').prop('required', false);
				} else {
					$('#billing_codice_sdi_field, #billing_pec_field').hide();
					$('#billing_fiscal_field, #billing_fiscal_repeat_field').hide();
				}
			}
		},

		/**
		 * Handle country change
		 */
		countryChangeHandler: function() {
			const self = this;

			$('#billing_country').on('change', function() {
				self.updateFieldsVisibility();
			});
		},

		/**
		 * Fiscal code validation (Italian)
		 */
		fiscalCodeValidation: function() {
			$('#billing_fiscal').on('blur', function() {
				const fiscalCode = $(this).val().toUpperCase();

				if (fiscalCode.length === 0) {
					return;
				}

				if (!TutoribaltoCheckout.validateFiscalCode(fiscalCode)) {
					$(this).addClass('woocommerce-invalid');
					$(this).after('<span class="error-message">' + tutoribaltoData.strings.invalidFiscal + '</span>');
				} else {
					$(this).removeClass('woocommerce-invalid');
					$(this).next('.error-message').remove();
				}
			});
		},

		/**
		 * Email verification
		 */
		emailVerification: function() {
			$('#billing_verify_email').on('blur', function() {
				const email = $('#billing_email').val();
				const verifyEmail = $(this).val();

				if (email !== verifyEmail) {
					$(this).addClass('woocommerce-invalid');
					$(this).after('<span class="error-message">' + tutoribaltoData.strings.invalidEmail + '</span>');
				} else {
					$(this).removeClass('woocommerce-invalid');
					$(this).next('.error-message').remove();
				}
			});
		},

		/**
		 * Translate form labels based on language
		 */
		translateLabels: function() {
			const lang = tutoribaltoData.currentLanguage;
			const labels = this.getLabelTranslations(lang);

			// Apply translations
			Object.keys(labels).forEach(function(selector) {
				$(selector).html(labels[selector]);
			});
		},

		/**
		 * Get label translations for language
		 */
		getLabelTranslations: function(lang) {
			const translations = {
				'en': {
					"label[for='billing_client_type_corp']": 'Company',
					"label[for='billing_client_type_private']": 'Private',
					"label[for='billing_first_name']": 'First name <abbr class="required" title="required">*</abbr>',
					"label[for='billing_last_name']": 'Last name <abbr class="required" title="required">*</abbr>',
					"label[for='billing_email']": 'Email address <abbr class="required" title="required">*</abbr>',
					"label[for='billing_phone']": 'Phone <abbr class="required" title="required">*</abbr>',
				},
				'de': {
					"label[for='billing_client_type_corp']": 'Unternehmen',
					"label[for='billing_client_type_private']": 'Privatgelände',
					"label[for='billing_first_name']": 'Vorname <abbr class="required" title="required">*</abbr>',
					"label[for='billing_last_name']": 'Nachname <abbr class="required" title="required">*</abbr>',
					"label[for='billing_email']": 'E-Mail-Adresse <abbr class="required" title="required">*</abbr>',
					"label[for='billing_phone']": 'Telefon <abbr class="required" title="required">*</abbr>',
				},
				'fr': {
					"label[for='billing_client_type_corp']": 'Société',
					"label[for='billing_client_type_private']": 'Privé',
					"label[for='billing_first_name']": 'Prénom <abbr class="required" title="required">*</abbr>',
					"label[for='billing_last_name']": 'Nom <abbr class="required" title="required">*</abbr>',
					"label[for='billing_email']": 'Adresse de messagerie <abbr class="required" title="required">*</abbr>',
					"label[for='billing_phone']": 'Téléphone <abbr class="required" title="required">*</abbr>',
				},
				'es': {
					"label[for='billing_client_type_corp']": 'Empresa',
					"label[for='billing_client_type_private']": 'Privado',
					"label[for='billing_first_name']": 'Nombre <abbr class="required" title="required">*</abbr>',
					"label[for='billing_last_name']": 'Apellidos <abbr class="required" title="required">*</abbr>',
					"label[for='billing_email']": 'Correo electrónico <abbr class="required" title="required">*</abbr>',
					"label[for='billing_phone']": 'Teléfono <abbr class="required" title="required">*</abbr>',
				}
			};

			return translations[lang] || {};
		},

		/**
		 * Validate Italian fiscal code
		 */
		validateFiscalCode: function(code) {
			if (code.length !== 16) {
				return false;
			}

			return /^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/.test(code);
		}
	};

})(jQuery);
