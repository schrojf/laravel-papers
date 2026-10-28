const STORAGE_KEY = 'papers.filter';

const WORD_CHARACTER = /[\p{L}\p{N}]/u;
const WORD_SEPARATOR = /[^\p{L}\p{N}]+/u;

// Points for a query word found in a field: at the start of a word, or anywhere
// inside it. Fields without points for "inside" match only at the start of a word.
const WEIGHTS = {
    name: { start: 8, inside: 4 },
    keywords: { start: 6, inside: 0 },
    handler: { start: 2, inside: 1 },
    description: { start: 1, inside: 0 },
};

/**
 * Lower case the text and remove its diacritics, so "stitok" finds "Štítok".
 * Remembers where each character came from in the original text, so a match
 * can be highlighted there even when the normalized text differs in length.
 */
function normalize(text) {
    let value = '';
    const offsets = [];
    let offset = 0;

    for (const character of text) {
        const normalized = character.normalize('NFD').replace(/\p{M}/gu, '').toLowerCase();

        for (let i = 0; i < normalized.length; i++) {
            offsets.push(offset);
        }

        value += normalized;
        offset += character.length;
    }

    offsets.push(offset);

    return { value, offsets };
}

/**
 * Find the term in the field, preferring an occurrence at the start of a word.
 */
function find(field, term, inside) {
    let fallback = null;

    for (let index = field.value.indexOf(term); index !== -1; index = field.value.indexOf(term, index + 1)) {
        if (index === 0 || ! WORD_CHARACTER.test(field.value[index - 1])) {
            return { index, start: true };
        }

        if (inside && ! fallback) {
            fallback = { index, start: false };
        }
    }

    return fallback;
}

/**
 * Score the paper for the query terms. Every term has to be found somewhere.
 */
function score(item, terms) {
    let total = 0;
    const highlights = [];

    for (const term of terms) {
        let best = 0;

        for (const [field, weights] of Object.entries(WEIGHTS)) {
            const found = find(item.fields[field], term, weights.inside > 0);

            if (! found) {
                continue;
            }

            best = Math.max(best, found.start ? weights.start : weights.inside);

            if (field === 'name') {
                highlights.push([found.index, found.index + term.length]);
            }
        }

        if (best === 0) {
            return null;
        }

        total += best;
    }

    return { total, highlights };
}

function highlight(item, ranges) {
    if (ranges.length === 0) {
        item.name.innerHTML = item.nameHtml;

        return;
    }

    const { offsets } = item.fields.name;
    const merged = [];

    ranges
        .map(([start, end]) => [offsets[start], offsets[end]])
        .sort((a, b) => a[0] - b[0])
        .forEach(([start, end]) => {
            const last = merged[merged.length - 1];

            if (last && start <= last[1]) {
                last[1] = Math.max(last[1], end);
            } else {
                merged.push([start, end]);
            }
        });

    const nodes = [];
    let position = 0;

    for (const [start, end] of merged) {
        nodes.push(document.createTextNode(item.text.slice(position, start)));

        const mark = document.createElement('mark');
        mark.className = 'bg-yellow-200 text-current rounded-sm';
        mark.textContent = item.text.slice(start, end);
        nodes.push(mark);

        position = end;
    }

    nodes.push(document.createTextNode(item.text.slice(position)));

    item.name.replaceChildren(...nodes);
}

function setActive(filter, index, scroll = false) {
    filter.results[filter.activeIndex]?.link.removeAttribute('data-active');
    filter.activeIndex = index;

    const item = filter.results[index];

    if (item) {
        item.link.setAttribute('data-active', '');

        if (scroll) {
            item.element.scrollIntoView({ block: 'nearest' });
        }
    }
}

function apply(filter, query) {
    const terms = normalize(query).value.split(WORD_SEPARATOR).filter(Boolean);
    const results = [];

    setActive(filter, -1);

    for (const item of filter.items) {
        const match = terms.length > 0 ? score(item, terms) : { total: 0, highlights: [] };

        item.element.hidden = ! match;
        highlight(item, match ? match.highlights : []);

        if (match) {
            results.push({ item, total: match.total });
        }
    }

    // Best matches first; papers matching equally keep their registered order.
    results.sort((a, b) => b.total - a.total || a.item.position - b.item.position);
    results.forEach(({ item }) => filter.list.appendChild(item.element));

    filter.results = results.map(({ item }) => item);
    filter.list.hidden = results.length === 0;

    if (filter.empty) {
        filter.empty.hidden = results.length > 0;
    }

    if (terms.length > 0 && document.activeElement === filter.input) {
        setActive(filter, 0);
    }
}

function createFilter(input) {
    const list = document.getElementById(input.getAttribute('aria-controls'));

    if (! list) {
        return null;
    }

    const items = Array.from(list.querySelectorAll('[data-paper]')).map((element, position) => {
        const data = JSON.parse(element.dataset.paper);
        const name = element.querySelector('[data-paper-name]');

        return {
            element,
            position,
            name,
            nameHtml: name.innerHTML,
            text: data.name,
            link: element.querySelector('a'),
            fields: {
                name: normalize(data.name),
                keywords: normalize(data.keywords.join(' ')),
                handler: normalize(data.handler),
                description: normalize(data.description),
            },
        };
    });

    return {
        input,
        list,
        items,
        empty: document.getElementById(`${list.id}-empty`),
        results: items,
        activeIndex: -1,
    };
}

function storedQuery() {
    try {
        return sessionStorage.getItem(STORAGE_KEY) ?? '';
    } catch {
        return '';
    }
}

function storeQuery(query) {
    try {
        if (query) {
            sessionStorage.setItem(STORAGE_KEY, query);
        } else {
            sessionStorage.removeItem(STORAGE_KEY);
        }
    } catch {
        // The filter works without remembering the query.
    }
}

function isEditable(target) {
    return target instanceof Element
        && (target.isContentEditable || target.closest('input, textarea, select') !== null);
}

/**
 * Filter the lists of papers controlled by the filter inputs. All filters on
 * the page share one query, which is remembered for the browser tab.
 */
export function initFilters() {
    const filters = Array.from(document.querySelectorAll('[data-papers-filter]'))
        .map(createFilter)
        .filter(Boolean);

    if (filters.length === 0) {
        return;
    }

    const update = (query) => {
        storeQuery(query);

        for (const filter of filters) {
            if (filter.input.value !== query) {
                filter.input.value = query;
            }

            apply(filter, query);
        }
    };

    for (const filter of filters) {
        const { input } = filter;

        input.addEventListener('input', () => update(input.value));
        input.addEventListener('focus', () => apply(filter, input.value));
        input.addEventListener('blur', () => setActive(filter, -1));

        input.addEventListener('keydown', (event) => {
            const count = filter.results.length;

            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();

                if (count > 0) {
                    const step = event.key === 'ArrowDown' ? 1 : -1;
                    const next = filter.activeIndex === -1
                        ? (step > 0 ? 0 : count - 1)
                        : (filter.activeIndex + step + count) % count;

                    setActive(filter, next, true);
                }
            } else if (event.key === 'Enter') {
                const item = filter.results[filter.activeIndex];

                if (item) {
                    event.preventDefault();
                    window.location.href = item.link.href;
                }
            } else if (event.key === 'Escape' && input.value !== '') {
                // Clear the query first; the next Escape closes the mobile menu.
                event.stopPropagation();
                event.preventDefault();
                update('');
            }
        });
    }

    // Like on GitHub, "/" focuses the filter. The list in the page content is
    // preferred over the one in the navigation, which opens on small screens.
    document.addEventListener('keydown', (event) => {
        if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey || isEditable(event.target)) {
            return;
        }

        const filter = filters.find(({ input }) => ! input.closest('nav')) ?? filters[0];

        event.preventDefault();

        if (filter.input.getClientRects().length === 0) {
            document.getElementById('menu-toggle')?.click();
        }

        filter.input.focus();
        filter.input.select();
    });

    const query = storedQuery();

    if (query) {
        update(query);
    }
}
