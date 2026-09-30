import { Feather } from '@expo/vector-icons';
import { Link, router } from 'expo-router';
import { View } from 'react-native';

import { AuthFrame } from '@/components/AuthFrame';
import { Button, Chip, Divider, Input, Row } from '@/components/ui/primitives';
import { Txt } from '@/components/ui/Txt';
import { DEMO_ACCOUNTS } from '@/data/seed';
import { useStore } from '@/data/store';
import { useI18n } from '@/i18n';
import { useTheme } from '@/theme/ThemeProvider';

/**
 * Sign-in placeholder: the fields are decorative and « Se connecter » always works — it opens the
 * admin account, which sees the whole platform. « Voir en tant que » opens the other demo roles.
 */
export default function SignIn() {
  const { d } = useI18n();
  const { colors } = useTheme();
  const { actions } = useStore();

  const roles: [string, string][] = [
    [d.auth.demoMember, DEMO_ACCOUNTS.member],
    [d.auth.demoEleve, DEMO_ACCOUNTS.eleve],
    [d.auth.demoDirection, DEMO_ACCOUNTS.direction],
    [d.auth.demoPending, DEMO_ACCOUNTS.pending],
  ];

  return (
    <AuthFrame title={d.auth.welcome} subtitle={d.auth.welcomeSub}>
      <View style={{ gap: 16 }}>
        <Input label={d.auth.email} icon="mail" autoCapitalize="none" keyboardType="email-address" autoComplete="email" placeholder={d.auth.emailPlaceholder} onSubmitEditing={() => actions.enterDemo()} />
        <Input label={d.auth.password} icon="lock" secureTextEntry autoComplete="password" placeholder="••••••••" onSubmitEditing={() => actions.enterDemo()} />
        <Link href="/mot-de-passe-oublie" style={{ alignSelf: 'flex-end' }}>
          <Txt variant="smallStrong" color="primary">{d.auth.forgot}</Txt>
        </Link>
        <Button label={d.auth.signIn} onPress={() => actions.enterDemo()} full size="lg" iconRight="arrow-right" />
        <Row gap={8} style={{ alignItems: 'flex-start' }}>
          <Feather name="info" size={14} color={colors.secondary} style={{ marginTop: 2 }} />
          <Txt variant="small" color="textMuted" style={{ flex: 1 }}>{d.auth.placeholderNote}</Txt>
        </Row>
        <Row gap={12}>
          <Divider style={{ flex: 1 }} />
          <Txt variant="small" color="textSubtle">{d.auth.noAccount}</Txt>
          <Divider style={{ flex: 1 }} />
        </Row>
        <Button label={d.auth.signUp} variant="secondary" full size="lg" onPress={() => router.push('/inscription')} />
        <View style={{ gap: 10, marginTop: 4 }}>
          <Txt variant="caption">{d.auth.viewAs}</Txt>
          <Row gap={8} wrap>
            {roles.map(([label, mail]) => (
              <Chip key={mail} label={label} onPress={() => actions.enterDemo(mail)} />
            ))}
          </Row>
        </View>
      </View>
    </AuthFrame>
  );
}
