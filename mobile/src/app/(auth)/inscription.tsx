import { Feather } from '@expo/vector-icons';
import { router } from 'expo-router';
import { useState } from 'react';
import { View } from 'react-native';

import { AuthFrame } from '@/components/AuthFrame';
import { Button, FieldRow, Input, Row, Segmented } from '@/components/ui/primitives';
import { Txt } from '@/components/ui/Txt';
import { useStore } from '@/data/store';
import { useI18n } from '@/i18n';
import { useTheme } from '@/theme/ThemeProvider';

/**
 * Sign-up placeholder: a short, optional form — « Créer mon compte » always works and opens the
 * platform straight away (demo mode, nothing is stored or checked).
 */
export default function SignUp() {
  const { d } = useI18n();
  const { colors } = useTheme();
  const { actions } = useStore();
  const [status, setStatus] = useState<'alumni' | 'eleve'>('alumni');

  return (
    <AuthFrame
      title={d.auth.signUpTitle}
      subtitle={d.auth.signUpSub}
      footer={
        <Row gap={6} style={{ justifyContent: 'center' }}>
          <Txt variant="small" color="textMuted">{d.auth.haveAccount}</Txt>
          <Txt variant="smallStrong" color="primary" onPress={() => router.replace('/connexion')}>{d.auth.signIn}</Txt>
        </Row>
      }>
      <View style={{ gap: 16 }}>
        <View style={{ gap: 6 }}>
          <Txt variant="smallStrong" color="textMuted">{d.auth.status}</Txt>
          <Segmented
            value={status}
            onChange={setStatus}
            options={[
              { value: 'alumni', label: d.roles.alumni, icon: 'award' },
              { value: 'eleve', label: d.roles.eleve, icon: 'book' },
            ]}
          />
        </View>
        <FieldRow>
          <Input label={d.auth.firstName} placeholder="Sarah" containerStyle={{ flex: 1 }} />
          <Input label={d.auth.lastName} placeholder="Martin" containerStyle={{ flex: 1 }} />
        </FieldRow>
        <Input label={d.auth.email} icon="mail" autoCapitalize="none" keyboardType="email-address" placeholder={d.auth.emailPlaceholder} />
        <FieldRow>
          <Input label={d.auth.promo} icon="award" keyboardType="number-pad" placeholder="2020" containerStyle={{ flex: 1 }} />
          <Input label={d.auth.school} icon="book" placeholder="Sciences Po" containerStyle={{ flex: 1.4 }} />
        </FieldRow>
        <Button label={d.auth.signUpCta} onPress={() => actions.enterDemo()} full size="lg" iconRight="arrow-right" />
        <Row gap={8} style={{ alignItems: 'flex-start' }}>
          <Feather name="info" size={14} color={colors.secondary} style={{ marginTop: 2 }} />
          <Txt variant="small" color="textMuted" style={{ flex: 1 }}>{d.auth.placeholderNote}</Txt>
        </Row>
      </View>
    </AuthFrame>
  );
}
