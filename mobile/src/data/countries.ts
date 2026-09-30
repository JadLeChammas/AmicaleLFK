import type { ContinentKey } from './types';

export type Country = {
  code: string;
  fr: string;
  en: string;
  flag: string;
  continent: ContinentKey;
  /** Latitude / longitude of the main alumni city — used by the globe. */
  ll: [number, number];
};

/** The Lycée Français de Koweït — origin of every arc on the globe and the map. */
export const LFK_LL: [number, number] = [29.33, 48.0];

export const COUNTRIES: Country[] = [
  { code: 'FR', fr: 'France', en: 'France', flag: '🇫🇷', continent: 'europe', ll: [48.86, 2.35] },
  { code: 'GB', fr: 'Royaume-Uni', en: 'United Kingdom', flag: '🇬🇧', continent: 'europe', ll: [51.51, -0.13] },
  { code: 'BE', fr: 'Belgique', en: 'Belgium', flag: '🇧🇪', continent: 'europe', ll: [50.85, 4.35] },
  { code: 'CH', fr: 'Suisse', en: 'Switzerland', flag: '🇨🇭', continent: 'europe', ll: [46.52, 6.63] },
  { code: 'ES', fr: 'Espagne', en: 'Spain', flag: '🇪🇸', continent: 'europe', ll: [40.42, -3.7] },
  { code: 'KW', fr: 'Koweït', en: 'Kuwait', flag: '🇰🇼', continent: 'asia', ll: [29.37, 47.98] },
  { code: 'LB', fr: 'Liban', en: 'Lebanon', flag: '🇱🇧', continent: 'asia', ll: [33.89, 35.5] },
  { code: 'AE', fr: 'Émirats arabes unis', en: 'United Arab Emirates', flag: '🇦🇪', continent: 'asia', ll: [24.45, 54.38] },
  { code: 'JP', fr: 'Japon', en: 'Japan', flag: '🇯🇵', continent: 'asia', ll: [35.68, 139.69] },
  { code: 'CA', fr: 'Canada', en: 'Canada', flag: '🇨🇦', continent: 'north_america', ll: [45.5, -73.57] },
  { code: 'US', fr: 'États-Unis', en: 'United States', flag: '🇺🇸', continent: 'north_america', ll: [40.71, -74.01] },
  { code: 'BR', fr: 'Brésil', en: 'Brazil', flag: '🇧🇷', continent: 'south_america', ll: [-23.55, -46.63] },
  { code: 'EG', fr: 'Égypte', en: 'Egypt', flag: '🇪🇬', continent: 'africa', ll: [30.04, 31.24] },
  { code: 'MA', fr: 'Maroc', en: 'Morocco', flag: '🇲🇦', continent: 'africa', ll: [33.57, -7.59] },
  { code: 'AU', fr: 'Australie', en: 'Australia', flag: '🇦🇺', continent: 'oceania', ll: [-33.87, 151.21] },
];

export const CONTINENTS: ContinentKey[] = ['europe', 'asia', 'north_america', 'africa', 'south_america', 'oceania'];

export const countryByCode = (code?: string) => COUNTRIES.find((c) => c.code === code);

export const countryName = (code: string | undefined, lang: 'fr' | 'en') => {
  const c = countryByCode(code);
  return c ? c[lang] : code ?? '';
};

/** Universities by country — used by the seed and suggested at sign-up. */
export const UNIVERSITIES: Record<string, string[]> = {
  FR: [
    'Paris 1 Panthéon-Sorbonne',
    'Université Paris Cité',
    'INSA Lyon',
    'ESSEC Business School',
    'Sciences Po',
    'École polytechnique',
    'Sorbonne Université',
    'HEC Paris',
    'Aix-Marseille Université',
  ],
  GB: ["King's College London", 'University College London', 'University of Edinburgh'],
  BE: ['Université libre de Bruxelles', 'UCLouvain'],
  CH: ['EPFL', 'Université de Genève'],
  ES: ['IE University'],
  KW: ['Kuwait University', 'American University of Kuwait', 'GUST'],
  LB: ['American University of Beirut', 'Université Saint-Joseph'],
  AE: ['Sorbonne Université Abu Dhabi', 'NYU Abu Dhabi'],
  JP: ['Waseda University'],
  CA: ['McGill University', 'Université de Montréal', 'HEC Montréal'],
  US: ['Columbia University', 'Boston University', 'New York University'],
  BR: ['Universidade de São Paulo'],
  EG: ["Université française d'Égypte"],
  MA: ['Université Mohammed VI Polytechnique'],
  AU: ['University of Sydney'],
};

export const CITY_BY_COUNTRY: Record<string, string[]> = {
  FR: ['Paris', 'Lyon', 'Marseille', 'Lille', 'Bordeaux'],
  GB: ['Londres', 'Édimbourg'],
  BE: ['Bruxelles', 'Louvain-la-Neuve'],
  CH: ['Lausanne', 'Genève'],
  ES: ['Madrid'],
  KW: ['Koweït City', 'Salmiya', 'Hawalli'],
  LB: ['Beyrouth'],
  AE: ['Abu Dhabi', 'Dubaï'],
  JP: ['Tokyo'],
  CA: ['Montréal', 'Toronto'],
  US: ['New York', 'Boston'],
  BR: ['São Paulo'],
  EG: ['Le Caire'],
  MA: ['Ben Guerir', 'Casablanca'],
  AU: ['Sydney'],
};
