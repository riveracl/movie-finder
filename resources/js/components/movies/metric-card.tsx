import { Card, CardContent } from '@/components/ui/card';
import type { MetricCardProps } from '@/types';

export default function MetricCard({
    label,
    value,
    icon: Icon,
}: MetricCardProps) {
    return (
        <Card className="border-white/15 bg-white/10 shadow-none backdrop-blur-sm dark:bg-white/5">
            <CardContent className="flex items-center gap-4 px-5 py-4">
                <div className="rounded-full bg-white/15 p-2.5 text-white">
                    <Icon className="size-4" />
                </div>
                <div>
                    <p className="text-xs tracking-[0.25em] text-white/70 uppercase">
                        {label}
                    </p>
                    <p className="text-lg font-semibold text-white">{value}</p>
                </div>
            </CardContent>
        </Card>
    );
}
