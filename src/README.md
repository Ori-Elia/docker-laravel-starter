## About


## Flow

## Development
重新建立並啟動容器
``` bash
docker-compose up -d
```

## Deploy

```bash
gcloud builds submit
```

## Cloud Run Job

### execute job
gcloud run jobs execute {job-name} --region=asia-east1 --wait

### delete job
gcloud run jobs delete {job-name} --region=asia-east1

### show job
gcloud run jobs list --region=asia-east1


