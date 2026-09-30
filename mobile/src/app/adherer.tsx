import { router } from 'expo-router';
import { View } from 'react-native';

import { Accordion, ClosingCta, EditorialImageHero, RuleColumns } from '@/components/site/blocks';
import { Reveal, Section, SerifHeading, SiteFrame } from '@/components/site/SiteFrame';
import { IMAGES } from '@/data/seed';
import { useStore } from '@/data/store';
import { useI18n } from '@/i18n';
import { useLayout } from '@/theme/layout';

/** « Adhérer » — who can join, how it works, FAQ. */
export default function Join() {
  const { d, f } = useI18n();
  const { me, actions } = useStore();
  const { isDesktop } = useLayout();
  const j = d.site.joinPage;
  const steps = [d.site.steps.s1, d.site.steps.s2, d.site.steps.s3].map((s, i) => ({ ...s, label: f(d.site.steps.stepLabel, { n: i + 1 }) }));

  return (
    <SiteFrame overlay>
      <EditorialImageHero
        tagline={d.site.steps.eyebrow}
        title={j.sub}
        description={d.site.features.private.d}
        image={IMAGES.friends}
        primary={me ? undefined : { label: j.cta, onPress: () => router.push('/inscription') }}
        secondary={me ? undefined : { label: d.site.hero.ctaSignIn, onPress: () => actions.enterDemo() }}
      />

      <Section style={{ paddingTop: 0 }}>
        <Reveal style={{ marginBottom: 32 }}>
          <SerifHeading title={j.whoTitle} />
        </Reveal>
        <RuleColumns items={[j.alumni, j.eleve, j.honneur]} />
      </Section>

      <Section tone="navy">
        <Reveal style={{ marginBottom: 32 }}>
          <SerifHeading title={d.site.steps.title} light />
        </Reveal>
        <RuleColumns items={steps} light />
      </Section>

      <Section>
        <View style={{ flexDirection: isDesktop ? 'row' : 'column', gap: isDesktop ? 64 : 24 }}>
          <Reveal style={{ width: isDesktop ? 340 : '100%' }}>
            <SerifHeading title={j.faqTitle} />
          </Reveal>
          <Reveal index={1} style={{ flex: 1 }}>
            <Accordion items={[j.q1, j.q2, j.q3, j.q4, j.q5]} />
          </Reveal>
        </View>
      </Section>

      <ClosingCta />
    </SiteFrame>
  );
}
