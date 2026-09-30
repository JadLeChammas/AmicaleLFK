import { router } from 'expo-router';
import type { ReactNode } from 'react';
import { View } from 'react-native';

import { EditorialImageHero } from '@/components/site/blocks';
import { Reveal, Section, SerifHeading, SiteFrame, useTone } from '@/components/site/SiteFrame';
import { Flag } from '@/components/ui/Flag';
import { LogoMark } from '@/components/ui/Logo';
import { Button } from '@/components/ui/primitives';
import { Txt } from '@/components/ui/Txt';
import { useI18n } from '@/i18n';
import { useLayout } from '@/theme/layout';
import { fonts, radius } from '@/theme/tokens';

const campus = require('@/assets/images/lfk-campus.png');

/** « Partenaires » — institutions supporting the association. */
export default function Partners() {
  const { d } = useI18n();
  const { isDesktop } = useLayout();
  const p = d.site.partnersPage;
  const partners: { key: string; logo: ReactNode; t: string; d: string }[] = [
    { key: 'lfk', logo: <LogoMark size={56} />, ...p.lfk },
    { key: 'scac', logo: <Flag code="FR" size={34} />, ...p.scac },
  ];

  return (
    <SiteFrame overlay>
      <EditorialImageHero tagline={d.site.partners.eyebrow} title={p.title} description={p.sub} image={campus} />

      <Section style={{ paddingTop: 0 }}>
        <PartnerList partners={partners} />
      </Section>

      <Section tone="blue">
        <View style={{ flexDirection: isDesktop ? 'row' : 'column', alignItems: isDesktop ? 'flex-end' : 'flex-start', justifyContent: 'space-between', gap: 24 }}>
          <Reveal style={{ flex: 1 }}>
            <SerifHeading title={p.becomeTitle} lead={p.becomeSub} />
          </Reveal>
          <Reveal index={1}>
            <Button label={p.becomeCta} onPress={() => router.push('/contact')} />
          </Reveal>
        </View>
      </Section>
    </SiteFrame>
  );
}

function PartnerList({ partners }: { partners: { key: string; logo: ReactNode; t: string; d: string }[] }) {
  const t = useTone();
  const { isMobile, isDesktop } = useLayout();
  return (
    <View style={{ borderTopWidth: 1, borderTopColor: t.rule }}>
      {partners.map((x, i) => (
        <Reveal key={x.key} index={i}>
          <View style={{ flexDirection: isDesktop ? 'row' : 'column', alignItems: isDesktop ? 'center' : 'flex-start', gap: isDesktop ? 40 : 16, paddingVertical: isMobile ? 28 : 44, borderBottomWidth: 1, borderBottomColor: t.rule }}>
            <Txt style={{ fontFamily: fonts.display, fontSize: 40, color: t.accent, width: 56 }}>{String(i + 1).padStart(2, '0')}</Txt>
            <View style={{ width: 96, height: 96, borderRadius: radius.hero, alignItems: 'center', justifyContent: 'center', backgroundColor: t.card }}>{x.logo}</View>
            <View style={{ flex: 1, gap: 8 }}>
              <Txt style={{ fontFamily: fonts.serif, fontSize: isMobile ? 30 : 40, lineHeight: isMobile ? 34 : 44, color: t.fg }}>{x.t}</Txt>
              <Txt style={{ fontFamily: fonts.regular, fontSize: 15, lineHeight: 24, color: t.muted, maxWidth: 560 }}>{x.d}</Txt>
            </View>
          </View>
        </Reveal>
      ))}
    </View>
  );
}
