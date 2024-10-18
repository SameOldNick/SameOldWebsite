import $ from "jquery";

interface ICountry {
    code_alpha2: string;
    code_alpha3: string;
    country: string;
    states: IState[];
}

interface IState {
    code: string;
    name: string;
}

// Load countries from JSON file
const loadCountries = async (sortBy?: 'code' | 'country'): Promise<ICountry[]> => {
    const countries: ICountry[] = (await import('./countries.json')).default;

    if (!sortBy)
        return countries;

    return countries.sort((a, b) => {
        if (sortBy === 'code')
            return a.code_alpha3.localeCompare(b.code_alpha3);
        else
            return a.country.localeCompare(b.country);
    });
}

// Populate the country select element
const populateCountries = ($el: JQuery<HTMLSelectElement>, countries: ICountry[], selected?: string) => {
    $el.empty();

    countries.forEach((country) => {
        $el.append(`<option value="${country.code_alpha3}"${country.code_alpha3 === selected ? ' selected' : ''}>${country.country}</option>`);
    });
}

const populateStates = ($el: JQuery<HTMLSelectElement>, states: IState[], selected?: string) => {
    // Populate the state select element
    states.forEach((state) => {
        $el.append(`<option value="${state.code}"${state.code === selected ? ' selected' : ''}>${state.name}</option>`);
    });

}

$(async () => {
    const $countrySelectEl = $<HTMLSelectElement>('select[data-type="country"]');

    if ($countrySelectEl.length > 0) {
        const initialCountry = $countrySelectEl.data('country');

        const sortCountriesBy =
            $countrySelectEl.prop('data-country-sort') && ['code', 'country'].includes($countrySelectEl.prop('data-country-sort')) ?
                $countrySelectEl.prop('data-country-sort') :
                'country';

        const countries = await loadCountries(sortCountriesBy);

        const $stateSelectEl = $<HTMLSelectElement>('select[data-type="state"]');
        const $stateLabelEl = $<HTMLSelectElement>('[data-type="state-label"]');

        populateCountries($countrySelectEl, countries, initialCountry);

        if ($stateSelectEl.length > 0) {
            const initialState = $stateSelectEl.data('state');

            // Event listener for country selection
            $countrySelectEl.on('change', function () {
                const selectedCountry = $(this).val();
                const sortStatesBy =
                    $stateSelectEl.prop('data-state-sort') && ['code', 'state'].includes($countrySelectEl.prop('data-state-sort')) ?
                        $stateSelectEl.prop('data-state-sort') :
                        'state';

                $stateSelectEl.empty().prop('disabled', true);

                const country = countries.find((country) => country.code_alpha3 === selectedCountry);

                // Hide select if no states for country
                if (!country || country.states.length === 0) {
                    $stateSelectEl.hide();
                    $stateLabelEl.hide();

                    return;
                }

                if (country) {
                    $stateSelectEl.show();
                    $stateLabelEl.show();

                    const states = country.states.sort((a, b) => sortStatesBy === 'code' ? a.code.localeCompare(b.code) : a.name.localeCompare(b.name));

                    populateStates($stateSelectEl, states, initialState);

                    $stateSelectEl.prop('disabled', false); // Enable the state select

                    // Ensure the initial state is selected if it exists
                    if (initialState) {
                        $stateSelectEl.val(initialState);
                    }
                }
            });

            // Trigger change event to populate the states if an initial country is selected
            if (initialCountry) {
                $countrySelectEl.trigger('change');
            }
        }
    }
});
