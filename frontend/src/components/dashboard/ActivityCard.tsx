interface Activity {
  time: string;
  user: string;
  activity: string;
}

interface ActivityCardProps {
  activities: Activity[];
}

export default function ActivityCard({ activities }: ActivityCardProps) {
  return (
    <section className="flex-1 overflow-hidden rounded-2xl bg-white/80 shadow-sm">
      <div className="border-b border-gray-200 px-6 py-4">
        <h2 className="text-lg font-bold text-gray-800">Aktivitas Terbaru</h2>
      </div>

      <div className="overflow-auto">
        <table className="w-full text-left">
          <thead>
            <tr className="text-sm text-gray-500">
              <th className="px-6 py-3">Waktu</th>
              <th className="px-6 py-3">User</th>
              <th className="px-6 py-3">Aktivitas</th>
            </tr>
          </thead>

          <tbody>
            {activities.map((item, index) => (
              <tr key={index} className="border-t border-gray-100 text-sm">
                <td className="px-6 py-3">{item.time}</td>
                <td className="px-6 py-3 font-medium">{item.user}</td>
                <td className="px-6 py-3">{item.activity}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </section>
  );
}
