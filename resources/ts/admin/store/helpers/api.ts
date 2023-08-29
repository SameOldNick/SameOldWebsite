/**
 * Sets the default state for each key.
 * All values in the object must have the same type for this to work.
 * @param keys Immutable array of keys in state object.
 * @param defaultValue Default value for each state item.
 * @returns The initial state.
 */
export const buildInitialState =
    <TKey extends string, TDefaultValue>(keys: readonly TKey[], defaultValue: TDefaultValue): Record<TKey, TDefaultValue> =>
        keys.reduce<Record<TKey, TDefaultValue>>((state, key) => ({
            [key]: defaultValue,
            ...state
        }), {} as Record<TKey, TDefaultValue>);

