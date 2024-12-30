import React from "react";
import { Input, InputProps } from "reactstrap";

import { createAuthRequest } from "@admin/utils/api/factories";

type CountriesProps = Omit<InputProps, 'type' | 'ref'>;

type TStateCountries = Record<string, ICountry>;

const Countries = React.forwardRef<Input, CountriesProps>(({ ...props }, ref) => {
    const [countries, setCountries] = React.useState<TStateCountries>({});

    const fetchCountries = React.useCallback(async () => {
        try {
            const response = await createAuthRequest().get<ICountry[]>('/countries');

            const newCountries: TStateCountries = {};

            for (const country of response.data) {
                newCountries[country.code] = country;
            }

            setCountries(newCountries);
        } catch (err) {
            logger.error(err);
        }
    }, []);

    React.useEffect(() => {
        fetchCountries();
    }, []);

    return (
        <>
            <Input ref={ref} type='select' {...props}>
                {Object.entries(countries).map(([code, { country }], index) => (
                    <option key={index} value={code}>
                        {`${country} (${code})`}
                    </option>
                ))}
            </Input>
        </>
    )
});

Countries.displayName = 'Countries';

export default Countries;
