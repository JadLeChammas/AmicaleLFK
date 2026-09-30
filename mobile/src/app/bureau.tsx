import { router } from 'expo-router';
import { View } from 'react-native';

import { EditorialImageHero, TeamShowcase, type TeamMember } from '@/components/site/blocks';
import { LinkCta, Reveal, Section, SerifHeading, SiteFrame } from '@/components/site/SiteFrame';
import { Button } from '@/components/ui/primitives';
import { IMAGES } from '@/data/seed';
import { fullName, useApprovedMembers, useStore } from '@/data/store';
import { useI18n } from '@/i18n';
import { useLayout } from '@/theme/layout';

/** « Le bureau » — the association's administrators and honorary members (Team Showcase). */
export default function Board() {
  const { d } = useI18n();
  const { me } = useStore();
  const { isDesktop } = useLayout();
  const members = useApprovedMembers();
  const b = d.site.board;
  // The longest-serving administrator presides; the others sit on the board.
  const admins = members.filter((u) => u.role === 'admin').sort((x, y) => (x.createdAt < y.createdAt ? -1 : 1));
  const honor = members.filter((u) => u.role === 'honneur');
  const open = (id: string) => (me ? () => router.push(`/membre/${id}`) : undefined);
  const team: TeamMember[] = [
    ...admins.map((u, i) => ({ id: u.id, name: fullName(u), role: i === 0 ? b.president : b.boardMember, image: u.avatar, onPress: open(u.id) })),
    ...honor.map((u) => ({ id: u.id, name: fullName(u), role: u.fonction ?? d.roles.honneur, image: u.avatar, onPress: open(u.id) })),
  ];

  return (
    <SiteFrame overlay>
      <EditorialImageHero tagline={d.app.long} title={b.title} description={b.sub} image={IMAGES.meeting} />

      <Section style={{ paddingTop: 0 }}>
        <Reveal style={{ marginBottom: 40 }}>
          <SerifHeading title={`${b.team} · ${b.honor}`} />
        </Reveal>
        <TeamShowcase members={team} />
      </Section>

      <Section tone="navy">
        <View style={{ flexDirection: isDesktop ? 'row' : 'column', alignItems: isDesktop ? 'flex-end' : 'flex-start', justifyContent: 'space-between', gap: 24 }}>
          <Reveal style={{ flex: 1 }}>
            <SerifHeading title={b.joinTitle} lead={b.joinSub} />
          </Reveal>
          <Reveal index={1} style={{ flexDirection: 'row', alignItems: 'center', gap: 16 }}>
            <Button label={b.joinCta} onPress={() => router.push('/contact')} />
            <LinkCta label={d.site.nav.join} onPress={() => router.push('/adherer')} />
          </Reveal>
        </View>
      </Section>
    </SiteFrame>
  );
}
