import React from 'react';
import {
  View,
  Text,
  Image,
  StyleSheet,
  Platform,
  StyleProp,
  ViewStyle,
  TextStyle,
  TouchableOpacity,
  Linking,
} from 'react-native';
import Feather from 'react-native-vector-icons/Feather';
import { moderateScale, scale, verticalScale, wp } from '../utils/responsive';

interface MarkdownRendererProps {
  content: string;
  style?: StyleProp<ViewStyle>;
  baseTextStyle?: StyleProp<TextStyle>;
  textColor?: string;
}

export const MarkdownRenderer: React.FC<MarkdownRendererProps> = ({
  content,
  style,
  baseTextStyle,
  textColor = '#334155',
}) => {
  if (!content) return null;

  const handleOpenLink = (url: string) => {
    if (!url) return;
    Linking.openURL(url).catch((err) =>
      console.warn('Gagal membuka URL:', err)
    );
  };

  // Helper to parse inline styles: **bold**, `code`, *italic*, [link](url)
  const renderInlineFormattedText = (
    rawText: string,
    overrideTextStyle?: StyleProp<TextStyle>
  ): React.ReactNode => {
    // Regex splits by **bold**, `code`, *italic*, or [link](url)
    const parts = rawText.split(/(\*\*.*?\*\*|`.*?`|\*.*?\*|\[.*?\]\(https?:\/\/.*?\))/g);

    return parts.map((part, index) => {
      if (!part) return null;

      // Link: [text](url)
      const linkMatch = part.match(/^\[(.*?)\]\((https?:\/\/.*?)\)$/);
      if (linkMatch) {
        const linkLabel = linkMatch[1];
        const linkUrl = linkMatch[2];
        return (
          <Text
            key={index}
            style={[styles.linkText, baseTextStyle, overrideTextStyle]}
            onPress={() => handleOpenLink(linkUrl)}
          >
            {linkLabel}
          </Text>
        );
      }

      // Bold: **text**
      if (part.startsWith('**') && part.endsWith('**') && part.length >= 4) {
        const inner = part.slice(2, -2);
        return (
          <Text
            key={index}
            style={[
              styles.baseText,
              { color: textColor },
              baseTextStyle,
              overrideTextStyle,
              styles.bold,
            ]}
          >
            {inner}
          </Text>
        );
      }

      // Inline code: `text`
      if (part.startsWith('`') && part.endsWith('`') && part.length >= 2) {
        const inner = part.slice(1, -1);
        return (
          <Text
            key={index}
            style={[
              styles.baseText,
              baseTextStyle,
              overrideTextStyle,
              styles.inlineCode,
            ]}
          >
            {inner}
          </Text>
        );
      }

      // Italic: *text*
      if (part.startsWith('*') && part.endsWith('*') && part.length >= 2) {
        const inner = part.slice(1, -1);
        return (
          <Text
            key={index}
            style={[
              styles.baseText,
              { color: textColor },
              baseTextStyle,
              overrideTextStyle,
              styles.italic,
            ]}
          >
            {inner}
          </Text>
        );
      }

      // Plain segment
      return (
        <Text
          key={index}
          style={[styles.baseText, { color: textColor }, baseTextStyle, overrideTextStyle]}
        >
          {part}
        </Text>
      );
    });
  };

  const lines = content.split('\n');

  return (
    <View style={[styles.container, style]}>
      {lines.map((rawLine, lineIndex) => {
        const trimmed = rawLine.trim();

        // Empty line spacer
        if (!trimmed) {
          return <View key={lineIndex} style={styles.spacer} />;
        }

        // Markdown Image: ![alt](url)
        const imageMatch = trimmed.match(/^!\[([^\]]*)\]\((https?:\/\/[^\s)]+)\)$/);
        // HTML Image: <img ... src="url" ... />
        const htmlImgMatch = trimmed.match(/<img[^>]+src=["'](https?:\/\/[^"']+)["'][^>]*>/i);

        if (imageMatch || htmlImgMatch) {
          const imgUrl = imageMatch ? imageMatch[2] : (htmlImgMatch ? htmlImgMatch[1] : '');
          const imgAlt = imageMatch ? imageMatch[1] : '';

          return (
            <View key={lineIndex} style={styles.imageCard}>
              <Image
                source={{ uri: imgUrl }}
                style={styles.responsiveImage}
                resizeMode="cover"
              />
              {imgAlt ? (
                <Text style={styles.imageCaption}>{imgAlt}</Text>
              ) : null}
            </View>
          );
        }

        // Video Detection: [video](url) or HTML <video ... src="url" ...> or YouTube
        const videoMatch = trimmed.match(/^\[(?:video|tonton|youtube|nonton)\]\((https?:\/\/[^\s)]+)\)$/i);
        const htmlVideoMatch = trimmed.match(/<video[^>]+src=["'](https?:\/\/[^"']+)["'][^>]*>/i);
        const isDirectVideoLink = trimmed.match(/^(?:https?:\/\/[^\s)]+\.(?:mp4|webm|ogg))$/i);
        const isYoutubeUrl = trimmed.match(/^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/[^\s]+$/i);

        if (videoMatch || htmlVideoMatch || isDirectVideoLink || isYoutubeUrl) {
          const videoUrl = videoMatch
            ? videoMatch[1]
            : (htmlVideoMatch ? htmlVideoMatch[1] : trimmed);

          return (
            <TouchableOpacity
              key={lineIndex}
              style={styles.videoCard}
              onPress={() => handleOpenLink(videoUrl)}
              activeOpacity={0.8}
            >
              <View style={styles.videoPlayCircle}>
                <Feather name="play" size={moderateScale(15)} color="#FFFFFF" />
              </View>
              <View style={styles.videoTextCol}>
                <Text style={styles.videoTitle} numberOfLines={1}>
                  Putar Video Pembaruan
                </Text>
                <Text style={styles.videoSubtitle} numberOfLines={1}>
                  {videoUrl}
                </Text>
              </View>
              <Feather name="external-link" size={moderateScale(14)} color="#047857" />
            </TouchableOpacity>
          );
        }

        // H1: # Heading
        if (rawLine.startsWith('# ')) {
          const text = rawLine.slice(2).trim();
          return (
            <View key={lineIndex} style={styles.h1Wrapper}>
              <Text style={[styles.h1, { color: textColor }]}>
                {renderInlineFormattedText(text, styles.h1)}
              </Text>
            </View>
          );
        }

        // H2: ## Heading
        if (rawLine.startsWith('## ')) {
          const text = rawLine.slice(3).trim();
          return (
            <View key={lineIndex} style={styles.h2Wrapper}>
              <Text style={[styles.h2, { color: textColor }]}>
                {renderInlineFormattedText(text, styles.h2)}
              </Text>
            </View>
          );
        }

        // H3: ### Heading
        if (rawLine.startsWith('### ')) {
          const text = rawLine.slice(4).trim();
          return (
            <View key={lineIndex} style={styles.h3Wrapper}>
              <Text style={[styles.h3, { color: textColor }]}>
                {renderInlineFormattedText(text, styles.h3)}
              </Text>
            </View>
          );
        }

        // H4: #### Heading
        if (rawLine.startsWith('#### ')) {
          const text = rawLine.slice(5).trim();
          return (
            <View key={lineIndex} style={styles.h4Wrapper}>
              <Text style={[styles.h4, { color: textColor }]}>
                {renderInlineFormattedText(text, styles.h4)}
              </Text>
            </View>
          );
        }

        // Blockquote: > text
        if (rawLine.startsWith('> ')) {
          const text = rawLine.slice(2).trim();
          return (
            <View key={lineIndex} style={styles.blockquote}>
              <Text style={styles.blockquoteText}>
                {renderInlineFormattedText(text, styles.blockquoteText)}
              </Text>
            </View>
          );
        }

        // Horizontal rule: --- or ***
        if (trimmed === '---' || trimmed === '***') {
          return <View key={lineIndex} style={styles.hr} />;
        }

        // Bullet list: * or - or •
        const bulletMatch = rawLine.match(/^(\s*)([-*•])\s+(.*)$/);
        if (bulletMatch) {
          const leadingSpaces = bulletMatch[1].length;
          const indentLevel = Math.min(Math.floor(leadingSpaces / 2), 3);
          const itemText = bulletMatch[3];
          const isNested = indentLevel > 0;

          return (
            <View
              key={lineIndex}
              style={[
                styles.bulletRow,
                { paddingLeft: scale(indentLevel * 14) },
              ]}
            >
              <View
                style={[
                  styles.bulletDot,
                  isNested && styles.bulletDotNested,
                ]}
              />
              <Text style={[styles.bulletContentText, { color: textColor }, baseTextStyle]}>
                {renderInlineFormattedText(itemText)}
              </Text>
            </View>
          );
        }

        // Numbered list: 1. or 2.
        const numMatch = rawLine.match(/^(\s*)(\d+)\.\s+(.*)$/);
        if (numMatch) {
          const leadingSpaces = numMatch[1].length;
          const indentLevel = Math.min(Math.floor(leadingSpaces / 2), 3);
          const num = numMatch[2];
          const itemText = numMatch[3];

          return (
            <View
              key={lineIndex}
              style={[
                styles.bulletRow,
                { paddingLeft: scale(indentLevel * 14) },
              ]}
            >
              <Text style={styles.numLabel}>{num}.</Text>
              <Text style={[styles.bulletContentText, { color: textColor }, baseTextStyle]}>
                {renderInlineFormattedText(itemText)}
              </Text>
            </View>
          );
        }

        // Standard Paragraph
        return (
          <Text
            key={lineIndex}
            style={[styles.paragraph, { color: textColor }, baseTextStyle]}
          >
            {renderInlineFormattedText(trimmed)}
          </Text>
        );
      })}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    width: '100%',
  },
  baseText: {
    fontSize: moderateScale(11.5),
    lineHeight: moderateScale(18),
  },
  spacer: {
    height: verticalScale(6),
  },
  bold: {
    fontWeight: '700',
    color: '#0F172A',
  },
  italic: {
    fontStyle: 'italic',
  },
  inlineCode: {
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
    backgroundColor: '#EEF2F6',
    color: '#047857',
    fontSize: moderateScale(10.5),
    paddingHorizontal: scale(4),
    paddingVertical: verticalScale(1),
    borderRadius: moderateScale(4),
    borderWidth: 1,
    borderColor: '#CBD5E1',
  },
  linkText: {
    fontSize: moderateScale(11.5),
    color: '#047857',
    fontWeight: '700',
    textDecorationLine: 'underline',
  },
  imageCard: {
    width: '100%',
    marginVertical: verticalScale(6),
    borderRadius: moderateScale(10),
    overflow: 'hidden',
    backgroundColor: '#F1F5F9',
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  responsiveImage: {
    width: '100%',
    height: verticalScale(140),
  },
  imageCaption: {
    paddingHorizontal: scale(8),
    paddingVertical: verticalScale(4),
    fontSize: moderateScale(10),
    color: '#64748B',
    fontStyle: 'italic',
  },
  videoCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ECFDF5',
    borderWidth: 1,
    borderColor: '#A7F3D0',
    borderRadius: moderateScale(10),
    padding: scale(10),
    marginVertical: verticalScale(6),
    gap: scale(10),
  },
  videoPlayCircle: {
    width: moderateScale(34),
    height: moderateScale(34),
    borderRadius: moderateScale(17),
    backgroundColor: '#047857',
    alignItems: 'center',
    justifyContent: 'center',
  },
  videoTextCol: {
    flex: 1,
  },
  videoTitle: {
    fontSize: moderateScale(12),
    fontWeight: '700',
    color: '#065F46',
  },
  videoSubtitle: {
    fontSize: moderateScale(10),
    color: '#047857',
    marginTop: verticalScale(1),
  },
  h1Wrapper: {
    marginTop: verticalScale(8),
    marginBottom: verticalScale(4),
    paddingBottom: verticalScale(4),
    borderBottomWidth: 1,
    borderBottomColor: '#E2E8F0',
  },
  h1: {
    fontSize: moderateScale(15),
    fontWeight: '800',
    letterSpacing: -0.3,
  },
  h2Wrapper: {
    marginTop: verticalScale(7),
    marginBottom: verticalScale(3),
    paddingBottom: verticalScale(3),
    borderBottomWidth: 1,
    borderBottomColor: '#F1F5F9',
  },
  h2: {
    fontSize: moderateScale(13.5),
    fontWeight: '800',
  },
  h3Wrapper: {
    marginTop: verticalScale(6),
    marginBottom: verticalScale(3),
  },
  h3: {
    fontSize: moderateScale(12.5),
    fontWeight: '700',
    color: '#047857',
  },
  h4Wrapper: {
    marginTop: verticalScale(4),
    marginBottom: verticalScale(2),
  },
  h4: {
    fontSize: moderateScale(11.5),
    fontWeight: '700',
  },
  bulletRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginTop: verticalScale(3),
    marginBottom: verticalScale(2),
  },
  bulletDot: {
    width: moderateScale(5),
    height: moderateScale(5),
    borderRadius: moderateScale(2.5),
    backgroundColor: '#047857',
    marginTop: verticalScale(6),
    marginRight: scale(7),
    flexShrink: 0,
  },
  bulletDotNested: {
    backgroundColor: '#94A3B8',
    width: moderateScale(4),
    height: moderateScale(4),
    borderRadius: moderateScale(2),
  },
  numLabel: {
    fontSize: moderateScale(11),
    fontWeight: '700',
    color: '#047857',
    marginRight: scale(6),
    minWidth: scale(16),
  },
  bulletContentText: {
    flex: 1,
    fontSize: moderateScale(11.5),
    lineHeight: moderateScale(17),
  },
  paragraph: {
    fontSize: moderateScale(11.5),
    lineHeight: moderateScale(17),
    marginBottom: verticalScale(4),
  },
  blockquote: {
    borderLeftWidth: 3,
    borderLeftColor: '#059669',
    backgroundColor: '#F0FDF4',
    paddingHorizontal: scale(10),
    paddingVertical: verticalScale(6),
    borderRadius: moderateScale(6),
    marginVertical: verticalScale(4),
  },
  blockquoteText: {
    fontSize: moderateScale(11),
    fontStyle: 'italic',
    color: '#065F46',
    lineHeight: moderateScale(16),
  },
  hr: {
    height: 1,
    backgroundColor: '#E2E8F0',
    marginVertical: verticalScale(8),
  },
});

export default MarkdownRenderer;
