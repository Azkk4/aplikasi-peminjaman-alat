import Sidebar from "../../components/sidebar/Sidebar";
import StatCard from "../../components/dashboard/StatCard";
import ProfileCard from "../../components/dashboard/ProfileCard";
import ActivityCard from "../../components/dashboard/ActivityCard";

export default function DashboardPage() {
  return (
    <div className="relative w-full h-full">
      <Sidebar />

      {/* Content */}
      <div
        className="
          absolute
          left-[35%]
          top-[17%]
          right-[4%]
          bottom-[3%]
          flex
          flex-col
          gap-6
        "
      >
        <header className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">Dashboard</h1>

          <ProfileCard name="Azka" role="Admin" />
        </header>

        <section className="grid grid-cols-3 gap-4">
          <StatCard title="Total User" value={0} />
          <StatCard title="Total Alat" value={0} />
          <StatCard title="Peminjaman" value={0} />
        </section>

        <ActivityCard
          activities={[
            {
              time: "10:30",
              user: "Admin",
              activity: "Menambahkan alat baru",
            },
            {
              time: "09:15",
              user: "Budi",
              activity: "Mengajukan peminjaman",
            },
            {
              time: "08:45",
              user: "Siti",
              activity: "Mengembalikan alat",
            },
          ]}
        />
      </div>
    </div>
  );
}
