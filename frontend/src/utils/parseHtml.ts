import { createElement } from 'react';
import type { ReactNode } from 'react';

export const parseHtml = (htmlContent: string): ReactNode[] => {
    const doc = new DOMParser().parseFromString(htmlContent, 'text/html');

    const parseNode = (node: Node, index: number): ReactNode => {
        if (node.nodeType === Node.TEXT_NODE) {
            return node.textContent;
        }
        if (node.nodeType === Node.ELEMENT_NODE) {
            const el = node as Element;
            const props: Record<string, string | number> = { key: index };

            if (el.className) props.className = el.className;
            if (el.id) props.id = el.id;
            const href = el.getAttribute('href');
            if (href) props.href = href;
            const src = el.getAttribute('src');
            if (src) props.src = src;
            const alt = el.getAttribute('alt');
            if (alt) props.alt = alt;

            const children = Array.from(el.childNodes).map((child, i) => parseNode(child, i));
            return createElement(el.tagName.toLowerCase(), props, ...children);
        }
        return null;
    };

    return Array.from(doc.body.childNodes).map((node, i) => parseNode(node, i));
};
