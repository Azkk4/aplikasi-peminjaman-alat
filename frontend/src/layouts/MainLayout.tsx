import { Outlet } from "react-router-dom";
import base from "../assets/Base.svg";

export default function MainLayout() {
  return (
    <div className="w-screen h-screen overflow-hidden">
      <div
        className="relative w-full h-full bg-cover bg-center bg-no-repeat"
        style={{
          backgroundImage: `url(${base})`,
        }}
      >
        <main className="absolute inset-0">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
