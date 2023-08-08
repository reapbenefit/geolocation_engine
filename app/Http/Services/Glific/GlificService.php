<?php

namespace App\Http\Services\Glific;

use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Cache;



class GlificService
{

    public function getToken()
    {
        return Cache::remember('glific_access_token', 60 * 9, function () {
            $response = Http::post(config('services.glific.api_url') . 'v1/session', [
                "user" => [
                    "phone" => config('services.glific.phone'),
                    "password" => config('services.glific.password')
                ]
            ]);

            return $response->json()['data']['access_token'];
        });
    }

    public function getClient()
    {
        $token = $this->getToken();
        return Http::withHeaders(['Authorization' => $token])
            ->asJson()
            ->baseUrl(config('services.glific.api_url'));
    }

    public function getContact($id)
    {
        $response =
            $this
            ->getClient()
            ->post('/', [
                'variables' => ['id' => $id],
                'query' => 'query contact($id: ID!) {
                        contact(id: $id) {
                            contact {  
                                id
                                name
                                phone
               
                            }
                        }
                    }'
            ]);

        return $response->json()['data']['contact']['contact'];
    }

    public function getContactByPhone($phone)
    {
        $key = 'contact_' . $phone;
        return Cache::remember($key, 5, function () use ($phone) {
            $response = $this->getClient()
                ->post('/', [
                    'variables' => [
                        'filter' => ["phone" => $phone],
                        'opts' => ["limit" => 1, "order" => "ASC", "orderWith" => "name"]
                    ],
                    'query' => 'query contacts($filter: ContactFilter!, $opts: Opts!) {
                    contacts(filter: $filter, opts: $opts) {
                        id
                        name
                        phone
                        fields
                    }
                }',
                ]);
            if ($response->json()['data']['contacts']) {
                return $response->json()['data']['contacts'][0];
            }
            return false;
        });
    }

    public function updateContactField($contact, $data)
    {
        $contactFields = json_decode($contact['fields'], true);
        $contactFields =  array_merge($contactFields, $this->formatContactFields($data));
        $contactFields = json_encode($contactFields);

        $response = $this->getClient()
            ->post('/', [
                'variables' => [
                    'id' => $contact['id'],
                    'input' => [
                        'fields' => $contactFields
                    ]
                ],
                'query' => 'mutation updateContact($id: ID!, $input:ContactInput!) {
                    updateContact(id: $id, input: $input) {
                      contact {
                        id
                        name
                        fields
                      }
                      errors {
                        key
                        message
                      }
                    }
                  }'
            ]);

        return $response->json()['data']['updateContact'];
    }

    public function resumeFlowForContact($contact, $flowID)
    {
        $response = $this->getClient()
            ->post('/', [
                'variables' => [
                    'flowId' => $flowID,
                    'contactId' => $contact['id'],
                    'result' => json_encode(
                        [
                            "custom_key" => [
                                "input" => "nothing",
                                "category" => "nothing",
                                "inserted_at" => "2022-02-21T13:44:44.168251Z"
                            ]
                        ]
                    )
                ],
                'query' => 'mutation resumeContactFlow($flowId: ID!, $contactId: ID!, $result: Json!) {
                    resumeContactFlow(flowId: $flowId, contactId: $contactId, result: $result) {
                      success
                      errors {
                        key
                        message
                      }
                    }
                  }'
            ]);

        $results = $response->json();

        if (isset($results['data'])) {
            return $results['data']['resumeContactFlow'];
        }

        return $response->body();
    }


    public function startFlowForContact($contactId, $flowID)
    {
        if (!$contactId || !$flowID) {
            return false;
        }

        $response = $this->getClient()
            ->post('/', [
                'variables' => [
                    'flowId' => $flowID,
                    'contactId' => $contactId,
                ],
                'query' => 'mutation startContactFlow($flowId: ID!, $contactId: ID!) {
                    startContactFlow(flowId: $flowId, contactId: $contactId) {
                      success
                      errors {
                        key
                        message
                      }
                    }
                  }'
            ]);

        $results = $response->json();

        if (isset($results['data'])) {
            return $results['data']['startContactFlow'];
        }

        return $response->body();
    }

    private function formatContactFields($data)
    {
        $results = [];
        foreach ($data as $key => $value) {
            $results[$key] = [
                'type' => 'string',
                'label' => $key,
                'value' => $value,
                'inserted_at' => date('Y-m-d H:i:s')
            ];
        }

        return $results;
    }
}
