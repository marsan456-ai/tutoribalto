# Tutoribalto Custom Gutenberg Blocks

## 📦 Blocks Disponibili

### 1. Product Filter Block

**Nome Block:** `tutoribalto/product-filter`

**Descrizione:**
Filtro prodotti interattivo per categoria/parte del corpo.

**Attributi:**
- `filterType` (string): Tipo di filtro - "tabs" o "dropdown" (default: "tabs")
- `categories` (array): Array di ID categorie WooCommerce da mostrare

**Utilizzo:**
```
<!-- wp:tutoribalto/product-filter {"filterType":"tabs","categories":[15,16,17,18]} /-->
```

**Funzionalità:**
- ✅ Tabs cliccabili per switchare tra categorie
- ✅ Caricamento dinamico prodotti via shortcode WC
- ✅ Supporto align wide/full
- ✅ Responsive design

**Esempio Frontend:**
```
[Zampe Anteriori] [Zampe Posteriori] [Testa] [Torace]
────────────────────────────────────────────────────
[Grid prodotti della categoria selezionata]
```

---

### 2. USP Section Block

**Nome Block:** `tutoribalto/usp-section`

**Descrizione:**
Sezione Unique Selling Points con icone SVG.

**Attributi:**
- `usps` (array): Array di USP objects con icon, title, text

**Struttura USP Object:**
```javascript
{
  icon: 'quality|vet|shipping',
  title: 'Titolo USP',
  text: 'Descrizione USP'
}
```

**Utilizzo:**
```
<!-- wp:tutoribalto/usp-section {"usps":[
  {"icon":"quality","title":"100% Qualità Italiana","text":"Prodotti realizzati in Italia"},
  {"icon":"vet","title":"Approccio Veterinario","text":"Consigliato dai veterinari"},
  {"icon":"shipping","title":"Spedizione Gratuita","text":"Su tutti gli ordini"}
]} /-->
```

**Funzionalità:**
- ✅ Icone SVG inline (quality, vet, shipping)
- ✅ Background gradient blu
- ✅ Grid responsive 3 colonne → 1 colonna mobile
- ✅ Testo bianco su sfondo colorato

**Esempio Frontend:**
```
┌─────────────────────────────────────────────────┐
│  [⭐]              [🏥]              [🚚]        │
│  100% Qualità     Approccio        Spedizione   │
│  Italiana         Veterinario      Gratuita     │
│  Prodotti         Consigliato      Su tutti     │
│  in Italia        dai veterinari   gli ordini   │
└─────────────────────────────────────────────────┘
```

---

### 3. Product Grid Block

**Nome Block:** `tutoribalto/product-grid`

**Descrizione:**
Griglia prodotti WooCommerce con titolo opzionale.

**Attributi:**
- `title` (string): Titolo sezione (opzionale)
- `category` (string): Slug categoria WooCommerce (opzionale)
- `limit` (number): Numero prodotti da mostrare (default: 8)
- `columns` (number): Numero colonne (default: 4)
- `orderby` (string): Ordinamento - "date", "popularity", "price" (default: "date")

**Utilizzo:**
```
<!-- wp:tutoribalto/product-grid {
  "title":"I Nostri Bestseller",
  "category":"tutori-zampe",
  "limit":12,
  "columns":4,
  "orderby":"popularity"
} /-->
```

**Funzionalità:**
- ✅ Usa shortcode WooCommerce `[products]`
- ✅ Filtro per categoria
- ✅ Ordinamento personalizzabile
- ✅ Titolo H2 opzionale
- ✅ Supporto align wide/full

**Esempio Frontend:**
```
I Nostri Bestseller
───────────────────────────────────────
[Prod 1] [Prod 2] [Prod 3] [Prod 4]
[Prod 5] [Prod 6] [Prod 7] [Prod 8]
```

---

## 🎨 Styling

### CSS Incluso

**File:** `assets/css/blocks-editor.css`

**Variabili CSS:**
```css
--color-primary: #04AAD7;    /* Blu Tutoribalto */
--color-secondary: #312D5A;  /* Viola scuro */
```

**Classi Principali:**
- `.tutoribalto-product-filter` - Container filtro
- `.products-tabs-title` - Lista tab filtro
- `.tutoribalto-usp-section` - Sezione USP
- `.usp-container` - Grid USP
- `.tutoribalto-product-grid` - Container griglia prodotti

---

## 🔧 Come Usare i Blocks

### Nell'Editor Gutenberg:

1. Clicca **"+"** per aggiungere block
2. Cerca **"Tutoribalto"** nella categoria dedicata
3. Seleziona il block desiderato:
   - Product Filter
   - USP Section
   - Product Grid

### Via Codice (template PHP):

```php
// Product Filter
echo do_blocks('<!-- wp:tutoribalto/product-filter {"categories":[15,16]} /-->');

// USP Section
echo do_blocks('<!-- wp:tutoribalto/usp-section /-->');

// Product Grid
echo do_blocks('<!-- wp:tutoribalto/product-grid {"title":"Prodotti in Offerta","limit":12} /-->');
```

---

## 🎯 Casi d'Uso

### Homepage E-Commerce

```
1. Hero Section (tema standard)
2. USP Section Block ← "Perché scegliere Balto"
3. Product Filter Block ← Filtro per parte del corpo
4. Product Grid Block ← Bestseller
5. Product Grid Block ← Nuovi arrivi
```

### Pagina Categoria

```
1. Titolo + Descrizione (WC standard)
2. Product Filter Block ← Sotto-categorie
3. Product Grid Block ← Prodotti categoria
```

### Landing Page

```
1. Hero custom
2. USP Section Block
3. Product Grid Block ← Featured products
4. Testimonials (tema standard)
5. CTA custom
```

---

## ⚙️ Personalizzazione

### Aggiungere Nuova Icona USP

**File:** `inc/blocks/class-block-loader.php`
**Metodo:** `get_usp_icon()`

```php
private function get_usp_icon( string $icon_name ): string {
    $icons = array(
        'quality'  => '<svg>...</svg>',
        'vet'      => '<svg>...</svg>',
        'shipping' => '<svg>...</svg>',
        // Aggiungi qui
        'custom'   => '<svg xmlns="http://www.w3.org/2000/svg">...</svg>',
    );
    return $icons[ $icon_name ] ?? '';
}
```

### Modificare Colori USP Section

**File:** `assets/css/blocks-editor.css`

```css
.tutoribalto-usp-section {
    /* Cambia gradient */
    background: linear-gradient(135deg, #YOUR_COLOR1 0%, #YOUR_COLOR2 100%);
}
```

### Aggiungere Nuovi Attributi

Esempio: aggiungere "backgroundColor" al Product Grid:

```php
'attributes' => array(
    // ... attributi esistenti
    'backgroundColor' => array(
        'type'    => 'string',
        'default' => '#ffffff',
    ),
),
```

Poi nel render:
```php
$bg_color = $attributes['backgroundColor'] ?? '#ffffff';
echo '<div style="background-color:' . esc_attr( $bg_color ) . '">';
```

---

## 🔌 Hooks Disponibili

### Filter: Block Output

```php
// Modifica output Product Filter
add_filter('tutoribalto_product_filter_output', function($output, $attributes) {
    // Personalizza HTML
    return $output;
}, 10, 2);
```

### Action: Before Block Render

```php
// Esegui codice prima del render
add_action('tutoribalto_before_block_render', function($block_name) {
    // Pre-processing
});
```

---

## 📱 Responsive Breakpoints

```css
Desktop:  > 768px  (Grid 3-4 colonne)
Tablet:   768px    (Grid 2 colonne)
Mobile:   < 768px  (Grid 1 colonna)
```

---

## ✅ Checklist Testing Blocks

- [ ] Product Filter: tabs switchano correttamente
- [ ] Product Filter: prodotti caricano via AJAX
- [ ] Product Filter: responsive mobile (dropdown)
- [ ] USP Section: icone visualizzate
- [ ] USP Section: gradient background corretto
- [ ] USP Section: responsive 3→1 colonne
- [ ] Product Grid: shortcode WC funziona
- [ ] Product Grid: filtro categoria applica
- [ ] Product Grid: ordinamento funziona
- [ ] Tutti: align wide/full funziona
- [ ] Editor: preview mode visibile

---

## 🐛 Troubleshooting

**Problema:** Blocks non appaiono nell'editor
**Soluzione:** Verifica `new Tutoribalto_Block_Loader()` in `functions.php`

**Problema:** Shortcode WC non funziona
**Soluzione:** Verifica WooCommerce attivo, categorie esistono

**Problema:** Icone USP non visualizzate
**Soluzione:** Check SVG syntax in `get_usp_icon()`, verifica nome icona

**Problema:** Stili non applicati
**Soluzione:** Clear cache, verifica `blocks-editor.css` enqueued

---

## 📚 Risorse

- [Gutenberg Block API](https://developer.wordpress.org/block-editor/reference-guides/block-api/)
- [WooCommerce Shortcodes](https://woocommerce.com/document/woocommerce-shortcodes/)
- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)

---

**Ultimo Aggiornamento:** 2025-01-17
**Versione Blocks:** 1.0.0
