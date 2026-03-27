import { createElement } from 'react';
import type { ReactNode } from 'react';

const SUPPORTED_TAGS = new Set([
    'p', 'br', 'strong', 'em', 'ul', 'ol', 'li', 'span', 'b', 'i', 'u',
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'a',
    'table', 'thead', 'tbody', 'tr', 'th', 'td'
]);

const sanitizeUri = (uri: string | null): string | undefined => {
    if (!uri) return undefined;
    const normalized = uri.trim().toLowerCase();
    if (normalized.startsWith('javascript:') || normalized.startsWith('data:')) {
        return undefined;
    }
    return uri;
};

export const parseHtml = (htmlContent: string): ReactNode[] => {
    const doc = new DOMParser().parseFromString(htmlContent, 'text/html');

    const parseNode = (node: Node, index: number): ReactNode => {
        if (node.nodeType === Node.TEXT_NODE) {
            return node.textContent;
        }

        if (node.nodeType === Node.ELEMENT_NODE) {
            const el = node as Element;
            const tagName = el.tagName.toLowerCase();

            if (!SUPPORTED_TAGS.has(tagName)) {
                return null;
            }

            const props: Record<string, string | number> = { key: index };

            if (el.className) props.className = el.className;
            if (el.id) props.id = el.id;

            const href = sanitizeUri(el.getAttribute('href'));
            if (href) props.href = href;

            const src = sanitizeUri(el.getAttribute('src'));
            if (src) props.src = src;

            const alt = el.getAttribute('alt');
            if (alt) props.alt = alt;

            const children = Array.from(el.childNodes)
                .map((child, i) => parseNode(child, i))
                .filter(child => child !== null);

            return createElement(tagName, props, ...children);
        }
        return null;
    };

    return Array.from(doc.body.childNodes)
        .map((node, i) => parseNode(node, i))
        .filter(node => node !== null);
};