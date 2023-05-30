import create from 'zustand'
import { persist } from 'zustand/middleware'
import { User } from '@library/api/User'

const storage = {
    getItem: async () => await User.getData(),
    setItem: async (_name, value) => await User.setData(value),
    removeItem: async () => await User.deleteData(),
}

const isGlobalLibraryEnabled = () =>
    window.extendifyData.sitesettings === null ||
    window.extendifyData?.sitesettings?.state?.enabled

export const useUserStore = create(
    persist(
        (set, get) => ({
            _hasHydrated: false,
            firstLoadedOn: new Date().toISOString(),
            email: '',
            apiKey: '',
            uuid: '',
            sdkPartner: '',
            noticesDismissedAt: {},
            modalNoticesDismissedAt: {},
            imports: 0, // total imports over time
            runningImports: 0, // timed imports, resets to 0 every month
            allowedImports: 0, // Max imports the Extendify service allows
            freebieImports: 0, //  Various free imports from actions (rewards)
            entryPoint: 'not-set',
            enabled: isGlobalLibraryEnabled(),
            canInstallPlugins: false,
            canActivatePlugins: false,
            openOnNewPage: undefined, // This is only being used on the server
            setOpenOnNewPage: (value) => set({ openOnNewPage: value }),
            incrementImports: () => {
                // If the user has freebie imports, use those first
                const freebieImports =
                    Number(get().freebieImports) > 0
                        ? Number(get().freebieImports) - 1
                        : Number(get().freebieImports)
                // If they don't, then increment the running imports
                const runningImports =
                    Number(get().runningImports) + +(freebieImports < 1)
                set({
                    imports: Number(get().imports) + 1,
                    runningImports,
                    freebieImports,
                })
            },
            giveFreebieImports: (amount) => {
                set({ freebieImports: get().freebieImports + amount })
            },
            totalAvailableImports: () => {
                return (
                    Number(get().allowedImports) + Number(get().freebieImports)
                )
            },
            hasAvailableImports: () => {
                return get().apiKey
                    ? true
                    : Number(get().runningImports) <
                          Number(get().totalAvailableImports())
            },
            remainingImports: () => {
                const remaining =
                    Number(get().totalAvailableImports()) -
                    Number(get().runningImports)
                // If they have no allowed imports, this might be a first load
                // where it's just fetching templates (and/or their max allowed)
                if (!get().allowedImports) {
                    return null
                }
                return remaining > 0 ? remaining : 0
            },
            // Will mark a modal or footer notice
            markNoticeSeen: (key, type) => {
                set({
                    [`${type}DismissedAt`]: {
                        ...get()[`${type}DismissedAt`],
                        [key]: new Date().toISOString(),
                    },
                })
            },
        }),
        {
            name: 'extendify-user',
            getStorage: () => storage,
            onRehydrateStorage: () => () => {
                useUserStore.setState({ _hasHydrated: true })
            },
            partialize: (state) => {
                delete state._hasHydrated
                return state
            },
        },
    ),
)
