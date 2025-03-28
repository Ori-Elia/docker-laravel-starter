<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $this->model->latest()->paginate($perPage, $columns, $pageName, $page);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);

        return $record->update($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function search($search = null, $searchFields = [], $perPage = null, array $with = [])
    {
        $query = $this->model->query();

        // 收集需要預載入的關聯
        $relationsToLoad = $with;

        // 如果有搜尋關鍵字和搜尋欄位
        if ($search && $searchFields) {
            // 從搜尋欄位中提取關聯名稱
            foreach ($searchFields as $field) {
                if (strpos($field, '.') !== false) {
                    [$relation, $relatedField] = explode('.', $field, 2);
                    if (! in_array($relation, $relationsToLoad)) {
                        $relationsToLoad[] = $relation;
                    }
                }
            }

            // 建立搜尋查詢
            $query->where(function ($q) use ($search, $searchFields) {
                foreach ($searchFields as $field) {
                    // 檢查是否為關聯欄位
                    if (strpos($field, '.') !== false) {
                        [$relation, $relatedField] = explode('.', $field, 2);

                        // 使用 whereHas 搜尋關聯欄位
                        $q->orWhereHas($relation, function ($subQuery) use ($relatedField, $search) {
                            $subQuery->where($relatedField, 'LIKE', "%{$search}%");
                        });
                    } else {
                        // 直接搜尋模型欄位
                        $q->orWhere($field, 'LIKE', "%{$search}%");
                    }
                }
            });
        }

        // 預載入所有需要的關聯
        if (! empty($relationsToLoad)) {
            $query->with($relationsToLoad);
        }

        return $query->latest()->paginate($perPage);
    }
}
