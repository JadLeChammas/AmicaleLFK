import { router } from 'expo-router';

import { ClosingCta, ContentGrid, EditorialImageHero, EditorialTestimonial, GlobeCard, RuleColumns, StatsRow, useDestinationMarkers, useQuoteCards } from '@/components/site/blocks';
import { Container, Reveal, Section, SerifHeading, SiteFrame } from '@/components/site/SiteFrame';
import { useCommunity } from '@/data/community';
import { IMAGES } from '@/data/seed';
import { useStore } from '@/data/store';
import { useI18n } from '@/i18n';

/** « L'Amicale » — mission, values and actions of the association. */
export default function Association() {
  const { d, f } = useI18n();
  const { me } = useStore();
  const c = useCommunity();
  const markers = useDestinationMarkers();
  const quotes = useQuoteCards();
  const a = d.site.association;

  return (
    <SiteFrame overlay>
      <EditorialImageHero
        tagline={d.app.long}
        title={a.sub}
        description={a.mission}
        image={IMAGES.graduation}
        primary={me ? undefined : { label: d.site.hero.ctaJoin, onPress: () => router.push('/inscription') }}
        secondary={{ label: d.site.nav.board, onPress: () => router.push('/bureau') }}
      />

      <Section style={{ paddingTop: 0 }}>
        <Reveal>
          <SerifHeading title={a.valuesTitle} />
        </Reveal>
        <Container style={{ paddingHorizontal: 0, marginTop: 32 }}>
          <RuleColumns items={[a.v1, a.v2, a.v3, a.v4]} />
        </Container>
      </Section>

      <Section tone="navy">
        <ContentGrid
          title={a.actionsTitle}
          items={[
            { title: a.a1.t, description: a.a1.d, image: IMAGES.party },
            { title: a.a2.t, description: a.a2.d, image: IMAGES.lecture },
            { title: a.a3.t, description: a.a3.d, image: IMAGES.students },
            { title: a.a4.t, description: a.a4.d, image: IMAGES.meeting },
          ]}
        />
      </Section>

      <Section tone="blue">
        <EditorialTestimonial quotes={quotes} />
      </Section>

      <Container style={{ paddingVertical: 64 }}>
        <GlobeCard
          compact
          title={f(a.reachTitle, { n: c.countries })}
          lead={d.site.world.sub}
          markers={markers}
          stats={<StatsRow light />}
        />
      </Container>
      <ClosingCta />
    </SiteFrame>
  );
}
