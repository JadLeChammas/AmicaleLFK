# Amicale LFK — application

Le réseau privé des anciens du Lycée Français de Koweït. Une seule base de code pour
**iPhone, Android et Web** (Expo SDK 57 + Expo Router).

Architecture, pages et design system : voir [DESIGN.md](DESIGN.md).

## Lancer le projet

```bash
npm install
npm run web        # navigateur — http://localhost:8081
npm run android    # émulateur / appareil Android (Expo Go)
npm run ios        # simulateur iOS (macOS) ou Expo Go
```

## Comptes de démonstration

Les données sont pour l'instant une **démo locale** (`src/data/seed.ts`), enregistrée sur
l'appareil. **Aucun mot de passe n'est demandé** : n'importe quel bouton « Se connecter » (page
d'accueil publique, en-tête du site, écran de connexion) ouvre directement le compte admin, qui voit
toute la plateforme. Sur l'écran de connexion, les boutons « Comptes de démonstration » ouvrent les
autres points de vue :

| Compte | E-mail | Accès |
|---|---|---|
| Admin | `jad@amicale-lfk.demo` | Espace membre + tableau de bord admin |
| Alumni | `sarah.martin@amicale-lfk.demo` | Espace membre |
| Élève | `nour.haddad@amicale-lfk.demo` | Espace membre |
| Direction | `direction@amicale-lfk.demo` | Proviseur (Membre d'honneur) : espace membre + publier, créer des événements, statistiques |
| En attente | `attente@amicale-lfk.demo` | Écran « compte en attente » |

(Le mot de passe `demo1234` n'est plus nécessaire en mode démo.) Paramètres → Zone sensible → « Réinitialiser les données de démo »
remet tout à zéro.

## Vérifications

```bash
npx tsc --noEmit
npx expo lint
```

## Prochaine étape

Brancher un vrai backend (Supabase : Auth, Postgres avec règles d'accès, stockage des photos)
à la place de `src/data/store.tsx`, sans changer les écrans.
