class LoadingSpinnerHelper {
    getPhasingElements(element) {
        let phasingElements = [];

        if ('psbitsSpinnerPhaseOut' in element.dataset) {
            phasingElements.push(element);
        }

        // Add all children of the element with that data attribute to the phasing elements
        element.querySelectorAll('[data-psbits-spinner-phase-out]').forEach((child) => {
            phasingElements.push(child);
        });

        return phasingElements;
    }

    phaseInElements(element) {
        element.querySelector('.spinner-border')?.remove();
        element.querySelector('.visually-hidden[role="status"]')?.remove();

        this.getPhasingElements(element).forEach((phasingElement) => {
            // Restore original content if it was phased out
            if (phasingElement.dataset.psbitsOriginalContent) {
                phasingElement.innerHTML = phasingElement.dataset.psbitsOriginalContent;
                delete phasingElement.dataset.psbitsOriginalContent;
            }
        });

        if (element.disabled) {
            element.disabled = false;
        } else if (element.classList.contains('disabled')) {
            element.classList.remove('disabled');
        }
    }

    phaseOutElements(element) {
        this.getPhasingElements(element).forEach((phasingElement) => {
            phasingElement.dataset.psbitsOriginalContent = phasingElement.innerHTML;
            phasingElement.innerHTML = '';
        });
    }
}

export default new LoadingSpinnerHelper();
