# Amicale LFK — Architecture UX & Design System

Application unique (iPhone · Android · Web) construite avec **Expo SDK 57 + Expo Router**.
Ce document fixe l'architecture, les pages, les composants et le design system avant le code.

---

## 1. Architecture UX — états d'accès

| État | Ce que voit l'utilisateur |
|---|---|
| Non connecté | `/connexion`, `/inscription`, `/mot-de-passe-oublie` + pages légales |
| Connecté, non approuvé | `/en-attente` uniquement |
| Session de récupération | `/nouveau-mot-de-passe` — prioritaire sur tout le reste |
| Approuvé (Alumni, Élève, Membre d'honneur) | Espace membre complet |
| Approuvé + Admin | Espace membre + `/admin/*` |

Le gating est **structurel** : `Stack.Protected` dans `src/app/_layout.tsx`. Une URL devinée ne
contourne pas le garde (et côté serveur, les politiques RLS Supabase appliquent les mêmes règles).

### Rôles et droits (`src/data/permissions.ts`)

| | Alumni / Élève | Membre d'honneur (direction du lycée) | Admin |
|---|---|---|---|
| Annuaire, Repère, Publications, Messages | ✓ | ✓ | ✓ |
| Événements et galeries photo | Alumni ✓ · Élève — | ✓ | ✓ |
| Publier un article, créer un événement | — | ✓ (gère ses propres contenus) | ✓ |
| Statistiques du réseau (lecture seule, `/statistiques`) | — | ✓ | ✓ (dans le tableau de bord) |
| Approbations, comptes, rôles, modération, contact, journal | — | — | ✓ |

- Le rôle Membre d'honneur est réservé au proviseur et à son assistante : il n'est **pas proposé à
  l'inscription**, seul un Admin l'attribue (avec un champ « Fonction », ex. « Proviseur »).
- Messagerie privée **désactivée entre la direction et les élèves** (mineurs), dans les deux sens.

## 2. Plan des routes

```
src/app/
  _layout.tsx                 Providers + gardes d'accès
  (auth)/connexion · inscription · mot-de-passe-oublie
  en-attente.tsx              Compte en attente d'approbation
  nouveau-mot-de-passe.tsx    Session de récupération
  (app)/_layout.tsx           Shell : sidebar (desktop) / bottom nav (mobile)
    index.tsx                 Accueil / dashboard
    annuaire/index · promo/[annee]
    membre/[id].tsx           Fiche membre
    repere.tsx                Continent → Pays → Universités
    evenements/index · [id]   Liste + page immersive avec galerie
    publications/index · [id]
    messages/index · [id]     Inbox + conversation
    profil/index · modifier   Profil + édition
    statistiques.tsx          Statistiques (direction + admins)
    parametres.tsx            Apparence, langue, compte, confidentialité, sécurité
    notifications.tsx
    admin/index · membres · approbations · contenus · contact · journal
  mentions-legales.tsx · plan-du-site.tsx · +not-found.tsx
```

## 3. Navigation

- **Desktop (≥ 1024 px)** : sidebar fixe 248 px — logo, sections (Accueil, Annuaire, Repère,
  Événements, Publications, Messages), puis Paramètres, Notifications et la carte avatar qui ouvre
  « Mon profil ». Section « Administration » visible seulement pour les admins.
- **Tablette (768–1023 px)** : sidebar compacte (icônes seules, 76 px).
- **Mobile (< 768 px)** : la barre de gauche devient la barre du bas, avec les mêmes rubriques —
  Accueil · Annuaire · Repère · Événements · Actus · Messages (sans Événements pour les élèves).
  Le profil s'ouvre avec l'avatar en haut à droite ; Paramètres, Admin et Statistiques sont dans le profil. Repère, Publications, Paramètres, Admin sont accessibles depuis l'Accueil
  (actions rapides) et le Profil (menu).
- **Header** : salutation + date/rôle à gauche ; recherche globale, notifications, avatar à droite.
  La recherche globale couvre membres, promos, pays, événements et publications.

## 4. Design system

### Couleurs — palette v2 (tokens dans `src/theme/tokens.ts`)
Drapeaux : images (`components/ui/Flag.tsx`) — les emojis drapeaux ne s'affichent pas sous Windows.

| Couleur | Hex | Rôle |
|---|---|---|
| Rouge | `#AE0000` | **Principal** — actions (boutons), éléments actifs, arcs et marqueurs du globe, bandeau d'appel final |
| Bleu | `#6680AE` | **Principal** — icônes, libellés secondaires, graphiques, points de la carte |
| Marine | `#00206A` | Accent — en-tête et pied du site, barre latérale, barres mobiles, carte du globe, bandeaux |
| Bleu clair | `#C8D3E5` | Accent — textes sur marine, bordures fortes, fonds doux (`secondarySoft`) |
| Blanc | `#FFFFFF` | Fond |

Tokens principaux : `primary` (rouge), `secondary` / `secondaryStrong` / `secondarySoft` (bleu),
`navy`, `sky`, `ink` (= marine en clair), `bg` / `surface` (blanc). Le mode sombre garde la même
logique sur un fond marine très profond (`#040B1F`).

### Typographie — éditoriale
- **Instrument Serif** (titres) : display 48, h1 36, titres de sections du site 30–60, chiffres clés.
- **Inter** (interface) : h2 18/600, h3 15/600, body 15/400, small 13, caption 11 capitales espacées.
- Accent éditorial : seconde ligne de titre en *Instrument Serif italique* rouge.

### Forme & espace
- Espacements : 4 · 8 · 12 · 16 · 20 · 24 · 32 · 48 ; sections du site 56 (mobile) / 88 (desktop).
- Rayons : 6 · input 8 · card 12 · hero 16 — plus de boutons « pilule ».
- Ombres discrètes (shadow-sm) ; filets fins (1 px) plutôt que des cartes partout.
- Animations d'entrée : fondu + montée, easing `[0.22, 1, 0.36, 1]`, décalage 0,1 s (`Reveal`).

### Composants 21st.dev (portés en React Native — `src/components/fx`, `src/components/site`)
Les composants d'origine sont en React web (canvas, Tailwind, framer-motion) ; ils sont réécrits
avec `react-native-svg` + Reanimated pour fonctionner sur iPhone, Android et web.

| Composant 21st.dev | Fichier | Utilisé dans |
|---|---|---|
| Interactive Globe (dev.yadhakim) | `fx/Globe.tsx`, `site/blocks.tsx` → `GlobeCard` | Accueil (app), landing, L'Amicale, Repère, connexion |
| World Map (Aceternity) | `fx/WorldMap.tsx` | Landing, Repère (vue carte) |
| Editorial Image Hero | `site/blocks.tsx` → `EditorialImageHero` | L'Amicale, Le bureau, Partenaires, Adhérer |
| Content Grid Section | `site/blocks.tsx` → `ContentGrid` | L'Amicale |
| Text Reveal (Mask) | `fx/MaskedText.tsx` (via `SerifHeading`) | Tous les titres du site |
| Text Roll | `fx/TextRoll.tsx` | Grands chiffres |
| Spinning Text With Icon | `fx/SpinningButton.tsx` | Appel final (bande rouge) |
| Team Showcase | `site/blocks.tsx` → `TeamShowcase` | Le bureau |
| Editorial Testimonial | `site/blocks.tsx` → `EditorialTestimonial` | Landing, L'Amicale |
| Footer with Suite | `site/SiteFrame.tsx` → `SiteFooter` | Toutes les pages publiques |
| Number Ticker / Marquee (Magic UI) | `fx/NumberTicker.tsx`, `fx/Marquee.tsx` | Chiffres clés, bandeau des universités |

Les points de terre (globe + carte) viennent de `dotted-map`, pré-calculés dans `src/data/worldDots.ts`.

### Mouvement et couleur — référence delassus.com
- **Diaporama des piliers** (`site/PillarSlider.tsx`) : la page d'accueil s'ouvre sur 4 volets pleine
  largeur (L'Amicale rouge, Annuaire bleu, Repère marine, Événements bordeaux `#7E0A14`). Le fond passe
  d'une couleur à l'autre, le titre glisse mot à mot, deux photos flottent, lecture automatique avec
  onglets qui se remplissent (pause au survol).
- **Bouton d'appel** (`site/BigCta.tsx`) : grand bouton arrondi avec flèche et ombre dans une teinte
  plus sombre de la bande.
- **Volet d'ouverture** (`SiteFrame` → `PageWipe`) : deux panneaux marine puis rouge glissent vers le haut
  à l'arrivée sur une page publique.
- **En-tête** : transparent sur le premier volet (`SiteFrame overlay`), marine dès que la page défile ;
  une ligne rouge suit la progression de lecture.
- **Au défilement** : titres (masque), blocs (`Reveal`), photos qui se dévoilent derrière un panneau de
  couleur (`fx/ImageReveal.tsx`), filets qui se tracent, arcs de la carte, chiffres qui basculent.
- **Harmonie** : chaque bande (`Section tone`) fournit ses couleurs aux blocs qu'elle contient
  (`useTone`) ; les accents viennent de la même famille de couleurs. Les couleurs de marques tierces
  (vert WhatsApp, drapeaux) restent d'origine.

### Composants réutilisables (`src/components/ui`)
`Screen` · `PageHeader` · `Card` · `SectionHeader` · `Button` (primary / secondary / ghost / danger /
onDark / white) · `IconButton` · `Input` · `SearchBar` · `Avatar` · `Badge` · `RoleBadge` ·
`Chip`/`Segmented` · `ListRow` · `EmptyState` · `BarChart` · `Donut` · `DateBadge` · `Lightbox` ·
`ConfirmDialog` · `Toast`. Composants métier : `MemberCard`, `EventCard`, `PublicationCard`, `ConversationRow`.

### Site public de l'association
`/bienvenue` (landing, première page hors connexion) · `/association` · `/bureau` · `/partenaires` ·
`/adherer` — accessibles connecté ou non (`src/components/site/SiteFrame.tsx`).

## 5. Données
`src/data/store.tsx` expose toutes les opérations (auth, membres, événements, galerie, messages,
admin…) via `useStore().actions`. L'implémentation actuelle est une **démo locale** (données de
`src/data/seed.ts`, persistées sur l'appareil) pour itérer sur l'UI. Prochaine étape : la
remplacer par Supabase (Auth, Postgres + RLS, Storage) sans changer les écrans.

Mode démo : « Se connecter » ouvre directement le compte admin, sans mot de passe (`actions.enterDemo`).
Les boutons de rôle sur l'écran de connexion ouvrent les autres comptes de démo.

## 6. i18n & thème
- `src/i18n` : dictionnaires FR (par défaut) / EN, extensibles.
- `src/theme` : `ThemeProvider` avec préférence Clair / Sombre / Système, persistée ; tout
  l'app lit les tokens via `useTheme()`.
