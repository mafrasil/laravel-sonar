import type { SonarEvent, SonarEventType, SonarState } from "./types";

// Initialize global state
window.__sonar = {
    queue: [],
    processing: false,
    impressed: new Set(),
    hovered: new Set(),
    observer: null,
    queueTimeout: null as ReturnType<typeof setTimeout> | null,
    config: {
        trackAllHovers: false,
    },
};

const processQueue = async () => {
    if (window.__sonar.processing || window.__sonar.queue.length === 0) return;

    window.__sonar.processing = true;
    const events = [...window.__sonar.queue];
    window.__sonar.queue = [];

    try {
        await fetch("/api/sonar/events", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ events }),
        });
    } catch (error) {
        window.__sonar.queue = [...events, ...window.__sonar.queue];
    }

    window.__sonar.processing = false;
};

export const queueEvent = (
    name: string,
    type: SonarEventType,
    metadata?: Record<string, any>
) => {
    const event: SonarEvent = {
        name,
        type,
        metadata,
        timestamp: Date.now(),
        page: window.location.pathname,
        userAgent: navigator.userAgent,
        screenSize: {
            width: window.innerWidth,
            height: window.innerHeight,
        },
    };

    window.__sonar.queue.push(event);

    // Debounce the processing
    if (window.__sonar.queueTimeout) {
        clearTimeout(window.__sonar.queueTimeout);
    }
    window.__sonar.queueTimeout = setTimeout(processQueue, 1000);
};

const handleInteraction = (event: Event, type: "click" | "hover") => {
    const target = event.target as HTMLElement;
    const element = target.closest("[data-sonar]");
    if (!element) return;

    const name = element.getAttribute("data-sonar");
    if (!name) return;

    const metadataAttr = element.getAttribute("data-sonar-metadata");
    const metadata = metadataAttr ? JSON.parse(metadataAttr) : undefined;

    if (!window.__sonar.config.trackAllHovers) {
        if (type === "hover" && window.__sonar.hovered.has(name)) return;
        if (type === "hover") window.__sonar.hovered.add(name);
    }

    queueEvent(name, type, metadata);
};

const detectImpressions = () => {
    document.querySelectorAll("[data-sonar]").forEach((element) => {
        const rect = element.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom >= 0;
        if (!isVisible) return;

        const name = element.getAttribute("data-sonar");
        if (!name || window.__sonar.impressed.has(name)) return;

        const metadataAttr = element.getAttribute("data-sonar-metadata");
        const metadata = metadataAttr ? JSON.parse(metadataAttr) : undefined;

        window.__sonar.impressed.add(name);
        queueEvent(name, "impression", metadata);
    });
};

const initSonar = () => {
    // Cleanup existing observers
    if (window.__sonar.observer) {
        window.__sonar.observer.disconnect();
    }

    // Setup DOM observer
    window.__sonar.observer = new MutationObserver(detectImpressions);
    window.__sonar.observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
    });

    // Setup event listeners
    document.addEventListener("click", (e) => handleInteraction(e, "click"));
    document.addEventListener("mouseover", (e) =>
        handleInteraction(e, "hover")
    );

    // Handle Inertia navigation
    document.addEventListener("inertia:start", () => {
        window.__sonar.impressed.clear();
        window.__sonar.hovered.clear();
    });

    // Initial impression detection
    detectImpressions();

    // Handle page unload
    window.addEventListener("beforeunload", () => {
        if (window.__sonar.queue.length > 0) {
            processQueue();
        }
    });
};

// Auto-initialize when the script loads
if (typeof window !== "undefined") {
    // Wait for DOM to be ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initSonar);
    } else {
        initSonar();
    }
}

// Export for manual initialization if needed
export { initSonar };

// Add a configuration function
export const configureSonar = (
    config: Partial<typeof window.__sonar.config>
) => {
    window.__sonar.config = {
        ...window.__sonar.config,
        ...config,
    };
};
