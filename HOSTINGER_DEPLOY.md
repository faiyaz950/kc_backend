# kc_backend on Hostinger

**Recommended:** deploy from GitHub → see **[GITHUB_DEPLOY.md](GITHUB_DEPLOY.md)**

**Manual ZIP deploy:** run `./deploy.sh` and follow zip upload steps below.

## Server info

| | |
|---|---|
| SSH | `ssh -p 65002 u163472436@145.79.211.230` |
| Laravel | `~/kc_backend/` |
| Web root | `~/domains/karbalaconnect.in/public_html/` |

## Manual ZIP (optional)

```bash
./deploy.sh
scp -P 65002 kc_backend_deploy.zip u163472436@145.79.211.230:~/
# then extract — see previous Hostinger zip instructions
```

## Verify

- https://karbalaconnect.in/up
- https://karbalaconnect.in/api/tracks
