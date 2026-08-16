import dashboardMarker from "../../assets/Dashboard.svg";

export default function Sidebar() {
  return (
    <aside className="absolute left-0 top-0 z-20 h-full w-[280px]">
      {/* Dashboard marker */}
      <img
        src={dashboardMarker}
        alt=""
        className="absolute left-0 top-[180px] w-full"
      />

      {/* Menu */}
      <nav className="relative z-10 flex flex-col pt-[180px]">
        <button className="h-[70px] text-left pl-20">Dashboard</button>

        <button className="h-[70px] text-left pl-20">Alat</button>

        <button className="h-[70px] text-left pl-20">Peminjaman</button>

        <button className="h-[70px] text-left pl-20">Pengembalian</button>
      </nav>
    </aside>
  );
}
