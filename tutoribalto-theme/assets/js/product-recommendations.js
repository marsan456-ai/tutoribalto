/**
 * Product Recommendations Script
 *
 * Automatically adds legal notices to product recommendation tabs.
 *
 * @package Tutoribalto_Theme
 */

(function() {
	'use strict';

	/**
	 * Add recommendation notices to product tabs
	 */
	function addNotices(root) {
		const panels = root.querySelectorAll('.woocommerce-Tabs-panel, .wc-tab, .woocommerce-tabs, .product .summary, .entry-summary');

		panels.forEach(function(panel) {
			const headings = panel.querySelectorAll('h5');

			headings.forEach(function(h5) {
				const text = (h5.textContent || '').trim().toUpperCase();

				// Check if this is a recommendations heading (multi-language)
				if (!(
					/RACCOMANDAZIONI\s+D['']USO/.test(text) ||       // IT
					/RECOMMENDATIONS\s+FOR\s+USE/.test(text) ||      // EN
					/RECOMMANDATIONS\s+D['']USAGE/.test(text) ||     // FR
					/ANWENDUNGSTIPPS/.test(text) ||                  // DE
					/RECOMENDACIONES\s+DE\s+USO/.test(text)          // ES
				)) {
					return;
				}

				// Find the UL element after the heading
				let ul = h5.nextElementSibling;
				while (ul && !(ul.tagName === 'UL' && /\blist\b/.test(ul.className))) {
					ul = ul.nextElementSibling;
				}

				if (!ul) {
					return;
				}

				// Check if notices already exist
				const exists = ul.innerHTML.includes('Il prodotto non è un presidio medico') ||
							ul.innerHTML.includes('This product is not a medical device') ||
							ul.innerHTML.includes('Ce produit n'est pas un dispositif médical');

				if (exists) {
					return;
				}

				// Detect language from heading text
				let lang = 'it';
				if (/RECOMMENDATIONS\s+FOR\s+USE/.test(text)) lang = 'en';
				else if (/RECOMMANDATIONS\s+D['']USAGE/.test(text)) lang = 'fr';
				else if (/ANWENDUNGSTIPPS/.test(text)) lang = 'de';
				else if (/RECOMENDACIONES\s+DE\s+USO/.test(text)) lang = 'es';

				// Create notice elements
				const liChild = document.createElement('li');
				liChild.className = 'star';
				const liNotMed = document.createElement('li');
				liNotMed.className = 'star';

				// Set text based on language
				switch(lang) {
					case 'en':
						liChild.textContent = 'Keep out of reach of children.';
						liNotMed.textContent = 'This product is not a medical device.';
						break;
					case 'fr':
						liChild.textContent = 'Tenir hors de portée des enfants.';
						liNotMed.textContent = 'Ce produit n'est pas un dispositif médical.';
						break;
					case 'de':
						liChild.textContent = 'Außer Reichweite von Kindern aufbewahren.';
						liNotMed.textContent = 'Dieses Produkt ist kein Medizinprodukt.';
						break;
					case 'es':
						liChild.textContent = 'Mantener fuera del alcance de los niños.';
						liNotMed.textContent = 'Este producto no es un dispositivo médico.';
						break;
					default: // it
						const liVet = document.createElement('li');
						liVet.className = 'star';
						liVet.textContent = 'Il prodotto non è un presidio medico.';
						ul.appendChild(liVet);

						liChild.textContent = 'Prodotto non detraibile come spesa veterinaria.';
						liNotMed.textContent = 'Tenere lontano dalla portata dei bambini.';
						break;
				}

				ul.appendChild(liChild);
				ul.appendChild(liNotMed);
			});
		});
	}

	// Initialize on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function() {
			addNotices(document);
		});
	} else {
		addNotices(document);
	}

	// Monitor DOM changes
	const observer = new MutationObserver(function() {
		addNotices(document);
	});

	observer.observe(document.body, {
		childList: true,
		subtree: true
	});

})();
