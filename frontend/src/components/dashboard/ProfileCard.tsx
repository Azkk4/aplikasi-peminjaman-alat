interface ProfileCardProps {
  name: string;
  role: string;
}

export default function ProfileCard({ name, role }: ProfileCardProps) {
  return (
    <div className="flex items-center gap-3 rounded-xl bg-white/80 px-4 py-3 shadow-sm">
      <div className="flex h-10 w-10 items-center justify-center rounded-full bg-[#d9e8c5] font-semibold">
        {name.charAt(0)}
      </div>

      <div>
        <p className="font-semibold">{name}</p>
        <p className="text-sm text-gray-500">{role}</p>
      </div>
    </div>
  );
}
