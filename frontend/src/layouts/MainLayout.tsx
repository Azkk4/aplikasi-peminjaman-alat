import { Outlet } from "react-router-dom";
import background from "../assets/Background.svg";
import decorations from "../assets/Decorations.svg";
import sidebar from "../assets/Sidebar.svg";
import paper from "../assets/Paper.svg";

export default function MainLayout() {
  return (
    <div className="relative min-h-screen overflow-hidden">
      
      {/* Background */}
      <img
        src={background}
        className="absolute inset-0 w-full h-full object-cover"
        alt=""
      />

      {/* Decorations */}
      <img
        src={decorations}
        className="absolute inset-0 w-full h-full object-cover pointer-events-none"
        alt=""
      />

      {/* Sidebar */}
      <img
        src={sidebar}
        className="absolute left-0 top-0 h-full"
        alt=""
      />

      {/* Paper / Content */}
      <img
        src={paper}
        className="absolute right-0 top-0 h-full"
        alt=""
      />

      {/* Isi halaman */}
      <main className="relative z-10">
        <Outlet />
      </main>

    </div>
  );
}