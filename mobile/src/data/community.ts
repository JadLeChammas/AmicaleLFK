import { useMemo } from 'react';

import { COUNTRIES, type Country } from './countries';
import { useApprovedMembers, useStore } from './store';

/**
 * Aggregate, non-personal figures about the network — safe to show on public pages.
 * Destinations follow the same population as Repère: alumni & admins with a university on file.
 */
export function useCommunity() {
  const members = useApprovedMembers();
  return useMemo(() => {
    const alumni = members.filter((u) => u.role === 'alumni' || u.role === 'admin');
    const abroad = alumni.filter((u) => u.country && u.school);
    const perCountry = new Map<string, number>();
    const perSchool = new Map<string, { n: number; country?: string }>();
    for (const u of abroad) {
      perCountry.set(u.country!, (perCountry.get(u.country!) ?? 0) + 1);
      perSchool.set(u.school!, { n: (perSchool.get(u.school!)?.n ?? 0) + 1, country: u.country });
    }
    const destinations: { country: Country; n: number }[] = COUNTRIES.filter((c) => perCountry.has(c.code))
      .map((c) => ({ country: c, n: perCountry.get(c.code)! }))
      .sort((a, b) => b.n - a.n);
    const schools = [...perSchool.entries()].map(([name, v]) => ({ name, ...v })).sort((a, b) => b.n - a.n);
    return {
      members: members.length,
      alumni: alumni.length,
      countries: new Set(members.map((u) => u.country).filter(Boolean)).size,
      promos: new Set(members.map((u) => u.promo).filter(Boolean)).size,
      universities: schools.length,
      destinations,
      schools,
    };
  }, [members]);
}

/**
 * Short quotes for the public pages, taken from what the board and the school leadership
 * published on the platform (first paragraph that isn't a greeting).
 */
const QUOTE_SOURCES = ['pub1', 'pub7', 'pub2'];

export function usePublishedQuotes(ids: string[] = QUOTE_SOURCES) {
  const { db } = useStore();
  return useMemo(() => {
    const admins = db.users.filter((u) => u.role === 'admin' && u.approved).sort((a, b) => (a.createdAt < b.createdAt ? -1 : 1));
    return ids
      .map((id) => db.publications.find((p) => p.id === id))
      .filter((p): p is NonNullable<typeof p> => !!p)
      .map((p) => {
        const author = db.users.find((u) => u.id === p.authorId);
        const para = p.body.split('\n').map((s) => s.trim()).find((s) => s.length > 40 && !/^(Chères?|Chers?)\b/.test(s)) ?? p.excerpt;
        const quote = para.length > 190 ? `${para.slice(0, para.lastIndexOf(' ', 180))}…` : para;
        return {
          quote,
          author: author ? `${author.firstName} ${author.lastName}` : '',
          image: author?.avatar,
          fonction: author?.fonction,
          isPresident: !!author && admins[0]?.id === author.id,
          role: author?.role,
          publicationId: p.id,
        };
      });
  }, [db.publications, db.users, ids]);
}
