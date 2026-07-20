import { usePage } from '@inertiajs/vue3'

export function useAsset() {
    const page = usePage()
    function asset(path) {
        const base = page.props.assetBase || ''
        return base + path
    }
    return { asset }
}
