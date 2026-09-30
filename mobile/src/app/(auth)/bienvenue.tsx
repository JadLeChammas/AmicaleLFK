import { router } from 'expo-router';
import { View } from 'react-native';

import { WorldMap } from '@/components/fx/WorldMap';
import { BigStatement, ClosingCta, EditorialTestimonial, GlobeCard, RuleColumns, StatsRow, UniversityRibbon, useDestinationMarkers, useQuoteCards } from '@/components/site/blocks';
import { PillarSlider, type PillarSlide } from '@/components/site/PillarSlider';
import { Container, Section, SerifHeading, SiteFrame, useTone } from '@/components/site/SiteFrame';
import { Flag } from '@/components/ui/Flag';
import { LogoMark } from '@/components/ui/Logo';
import { Txt } from '@/components/ui/Txt';
import { useCommunity } from '@/data/community';
import { IMAGES } from '@/data/seed';
import { useStore } from '@/data/store';
import { useI18n } from '@/i18n';
import { useLayout } from '@/theme/layout';
import { brand, fonts } from '@/theme/tokens';

const campus = require('@/assets/images/lfk-campus.png');

/**
 * Public landing page of the association — what signed-out visitors see first.
 * No white, after delassus.com: a colour-morphing slider for the pillars of the Amicale, then
 * full-bleed brand bands (navy, light blue, blue, red) whose content animates in as you scroll.
 */
export default function Landing() {
  const { d, f } = useI18n();
  const { me, actions } = useStore();
  const markers = useDestinationMarkers();
  const quotes = useQuoteCards();
  const steps = [d.site.steps.s1, d.site.steps.s2, d.site.steps.s3].map((s, i) => ({ ...s, label: f(d.site.steps.stepLabel, { n: i + 1 }) }));

  // Signed-out visitors enter the demo first, then land on the section they asked for.
  const open = (href: string) => () => {
    if (!me) actions.enterDemo();
    setTimeout(() => router.push(href as never), me ? 0 : 150);
  };
  const sl = d.site.slides;
  const slides: PillarSlide[] = [
    { key: 's0', ...sl.s0, text: d.site.hero.sub, onPress: () => router.push('/inscription'), bg: brand.red, fg: '#FFFFFF', muted: 'rgba(255,255,255,0.82)', accentColor: '#FFC4C5', ctaBg: '#FFFFFF', ctaFg: brand.red, shadow: '#4F0000', images: [campus, IMAGES.graduation] },
    { key: 's1', ...sl.s1, text: d.site.features.directory.d, onPress: open('/annuaire'), bg: brand.blue, fg: '#FFFFFF', muted: 'rgba(255,255,255,0.86)', accentColor: brand.navy, ctaBg: brand.navy, ctaFg: '#FFFFFF', shadow: '#2A3F69', images: [IMAGES.friends, IMAGES.group] },
    { key: 's2', ...sl.s2, text: d.site.features.repere.d, onPress: open('/repere'), bg: brand.navy, fg: '#FFFFFF', muted: 'rgba(200,211,229,0.88)', accentColor: brand.sky, ctaBg: brand.red, ctaFg: '#FFFFFF', shadow: '#000718', images: [IMAGES.students, IMAGES.paris] },
    { key: 's3', ...sl.s3, text: d.site.features.events.d, onPress: open('/evenements'), bg: '#7E0A14', fg: '#FFFFFF', muted: 'rgba(255,255,255,0.82)', accentColor: '#FFC4C5', ctaBg: '#FFFFFF', ctaFg: '#7E0A14', shadow: '#360004', images: [IMAGES.gala, IMAGES.party] },
  ];

  return (
    <SiteFrame overlay>
      <PillarSlider slides={slides} />

      <UniversityRibbon />

      {/* Giant figure — delassus.com "3000HA" */}
      <BigStatement tone="page" />

      {/* Globe — full-bleed navy */}
      <GlobeCard bleed title={d.site.world.title} lead={d.site.world.sub} markers={markers} stats={<StatsRow light />} />

      {/* Destinations — World Map */}
      <Section>
        <SerifHeading title={d.site.world.top} center />
        <Destinations />
      </Section>

      {/* Words from the board and the school */}
      <Section tone="blue">
        <EditorialTestimonial quotes={quotes} />
      </Section>

      {/* How to join */}
      <Section tone="navy">
        <View style={{ marginBottom: 40 }}>
          <SerifHeading title={d.site.steps.title} />
        </View>
        <RuleColumns items={steps} />
      </Section>

      <Partners />

      <ClosingCta />
    </SiteFrame>
  );
}

function Destinations() {
  const t = useTone();
  const { lang } = useI18n();
  const { isMobile } = useLayout();
  const c = useCommunity();
  return (
    <>
      <View style={{ marginTop: isMobile ? 24 : 40 }}>
        <WorldMap arcs={c.destinations.filter((x) => x.country.code !== 'KW').map((x) => ({ key: x.country.code, to: x.country.ll }))} fadeInto={t.bg} dotColor={t.fg} />
      </View>
      <View style={{ flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'center', columnGap: 32, rowGap: 12, marginTop: 24 }}>
        {c.destinations.slice(0, isMobile ? 6 : 8).map((x) => (
          <View key={x.country.code} style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
            <Flag code={x.country.code} size={12} />
            <Txt style={{ fontFamily: fonts.medium, fontSize: 13, color: t.muted }}>{x.country[lang]}</Txt>
            <Txt style={{ fontFamily: fonts.display, fontSize: 24, color: t.fg }}>{x.n}</Txt>
          </View>
        ))}
      </View>
    </>
  );
}

function Partners() {
  const t = useTone();
  const { d } = useI18n();
  const { isDesktop } = useLayout();
  return (
    <Container style={{ paddingVertical: 48, flexDirection: isDesktop ? 'row' : 'column', alignItems: isDesktop ? 'center' : 'flex-start', gap: isDesktop ? 48 : 20 }}>
      <Txt style={{ fontFamily: fonts.semibold, fontSize: 11, letterSpacing: 1.6, textTransform: 'uppercase', color: t.muted }}>{d.site.partners.eyebrow}</Txt>
      {[
        { key: 'lfk', logo: <LogoMark size={36} />, name: d.site.partnersPage.lfk.t },
        { key: 'scac', logo: <Flag code="FR" size={22} />, name: d.site.partnersPage.scac.t },
      ].map((p) => (
        <View key={p.key} style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
          {p.logo}
          <Txt style={{ fontFamily: fonts.serif, fontSize: 24, color: t.fg }}>{p.name}</Txt>
        </View>
      ))}
    </Container>
  );
}
