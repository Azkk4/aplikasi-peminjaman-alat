interface StatCardProps {
  title: string;
  value: number;
  icon?: React.ReactNode;
}

export default function StatCard({ title, value, icon }: StatCardProps) {
  return (
    <div className="flex min-h-[140px] flex-1 items-center justify-between rounded-2xl bg-white/80 px-6 py-5 shadow-sm">
      <div>
        <p className="text-sm font-medium text-gray-500">{title}</p>

        <p className="mt-2 text-3xl font-bold text-gray-800">{value}</p>
      </div>

      {icon && (
        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-[#e5efd9]">
          {icon}
        </div>
      )}
    </div>
  );
}
