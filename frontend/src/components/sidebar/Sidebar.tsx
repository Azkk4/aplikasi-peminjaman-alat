import { NavLink } from "react-router-dom";

const menuItems = [
  {
    label: "Dashboard",
    path: "/",
  },
  {
    label: "Alat",
    path: "/alat",
  },
  {
    label: "Kategori",
    path: "/kategori",
  },
  {
    label: "Peminjaman",
    path: "/peminjaman",
  },
  {
    label: "Pengembalian",
    path: "/pengembalian",
  },
];

export default function Sidebar() {
  return (
    <aside className="absolute left-[2%] top-[5%] z-20 w-[25%]">
      <div className="flex flex-col gap-3">
        {menuItems.map((item) => (
          <NavLink
            key={item.path}
            to={item.path}
            className={({ isActive }) =>
              `
              relative flex h-14 items-center
              rounded-xl px-6
              font-semibold
              transition-all
              ${isActive ? "bg-white/80 shadow-sm" : "hover:bg-white/40"}
              `
            }
          >
            {item.label}
          </NavLink>
        ))}
      </div>
    </aside>
  );
}
