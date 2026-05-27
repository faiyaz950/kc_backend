# Deploy kc_backend from GitHub → Hostinger

Repo: https://github.com/faiyaz950/kc_backend.git  
Domain: https://karbalaconnect.in

Every push to `main` deploys automatically via GitHub Actions.

---

## Part 1 — One-time: SSH key for GitHub Actions

On your Mac:

```bash
ssh-keygen -t ed25519 -f ~/.ssh/hostinger_kc_deploy -N ""
cat ~/.ssh/hostinger_kc_deploy.pub
```

1. **Hostinger hPanel** → `karbalaconnect.in` → **SSH Access** → **SSH keys** → paste public key → Add  
2. **GitHub** → repo **kc_backend** → **Settings** → **Secrets and variables** → **Actions** → **New repository secret**

| Secret name | Value |
|-------------|--------|
| `HOSTINGER_SSH_HOST` | `145.79.211.230` |
| `HOSTINGER_SSH_PORT` | `65002` |
| `HOSTINGER_SSH_USER` | `u163472436` |
| `HOSTINGER_SSH_PRIVATE_KEY` | Full contents of `~/.ssh/hostinger_kc_deploy` (private key) |

---

## Part 2 — One-time: Server setup (SSH)

hPanel: **PHP 8.3** + **MySQL** database create karo.

```bash
ssh -p 65002 u163472436@145.79.211.230
curl -sL https://raw.githubusercontent.com/faiyaz950/kc_backend/main/hostinger-setup-github.sh | bash
```

Ya repo clone ke baad:

```bash
cd ~
git clone https://github.com/faiyaz950/kc_backend.git kc_backend
cd kc_backend
chmod +x hostinger-setup-github.sh hostinger-post-deploy.sh
./hostinger-setup-github.sh
nano ~/kc_backend/.env
./hostinger-post-deploy.sh
```

`.env` mein hPanel MySQL + Cloudinary values daalo.

Test: https://karbalaconnect.in/up

---

## Part 3 — Auto deploy

`main` branch par push karo:

```bash
git add .
git commit -m "your message"
git push origin main
```

GitHub → **Actions** → **Deploy kc_backend to Hostinger** → run dekho.

---

## Manual deploy

GitHub → **Actions** → workflow → **Run workflow**.

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| `.env not found` | Server par `hostinger-setup-github.sh` chalao |
| Permission denied (SSH) | Public key Hostinger + private key GitHub secret check karo |
| DB connection error | `~/kc_backend/.env` credentials |
| 500 error | `~/kc_backend/storage/logs/laravel.log` |

`.env` kabhi GitHub par push mat karo — sirf server par rakho.
