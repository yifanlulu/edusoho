<?php
namespace Topxia\Service\Search\Impl;

use Topxia\Service\Common\BaseService;
use Topxia\Service\Search\SearchService;
use Topxia\Service\CloudPlatform\CloudAPIFactory;
use Topxia\Service\Search\Adapter\SearchAdapterFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class SearchServiceImpl extends BaseService implements SearchService
{
    public function cloudSearch($type, $conditions = array())
    {
        $api        = CloudAPIFactory::create('leaf');

        if($type === 'course'){
            $conditions['type'] = 'course,openCourse';
        }

        $conditions = $this->searchBase64Encode($conditions);

        $result     = $api->get('/search', $conditions);

        if (empty($result['success'])) {
            throw new \RuntimeException($this->getKernel()->trans('搜索失败，请稍候再试.'), 1);
        }

        if (empty($result['body']['datas'])) {
            return array(array(), 0);
        }

        $resultSet = $result['body']['datas'];
        $counts    = $result['body']['count'];

        $resultSet = SearchAdapterFactory::create($type)->adapt($resultSet);

        return array($resultSet, $counts);
    }

    public function refactorAllDocuments()
    {
        $api        = CloudAPIFactory::create('root');
        $conditions = array('categorys' => 'course,user,thread,article');
        return $api->post('/search/refactor_documents', $conditions);
    }

    public function applySearchAccount($callbackRouteUrl)
    {
        $siteUrl = $this->getSiteUrl();

        $api  = CloudAPIFactory::create('root');
        $urls = array(
            array('category' => 'course', 'url' => $siteUrl . '/api/courses?cursor=0&start=0&limit=100'),
            array('category' => 'lesson', 'url' => $siteUrl . '/api/lessons?cursor=0&start=0&limit=100'),
            array('category' => 'user', 'url' => $siteUrl . '/api/users?cursor=0&start=0&limit=100'),
            array('category' => 'thread', 'url' => $siteUrl . '/api/chaos_threads?cursor=0,0,0&start=0,0,0&limit=50'),
            array('category' => 'article', 'url' => $siteUrl . '/api/articles?cursor=0&start=0&limit=100'),
            array('category' => 'openCourse', 'url' => $siteUrl . '/api/open_courses?cursor=0&start=0&limit=100'),
            array('category' => 'openLesson', 'url' => $siteUrl . '/api/open_course_lessons?cursor=0&start=0&limit=100'),
        );
        $urls = urlencode(json_encode($urls));

        $callbackUrl = $siteUrl . $callbackRouteUrl;
        $sign        = $this->getSignEncoder()->encodePassword($callbackUrl, $api->getAccessKey());
        $callbackUrl .= '?sign=' . rawurlencode($sign);

        $result = $api->post("/search/accounts", array('urls' => $urls, 'callback' => $callbackUrl));
        
        if ($result['success']) {
            $this->setCloudSearchWaiting();
        }

        return !empty($result['success']);
    }

    protected function getSiteUrl()
    {
        $siteSetting = $this->getSettingService()->get('site');
        $siteUrl     = $siteSetting['url'];
        if (strpos($siteUrl, 'http://') !== 0) {
            $siteUrl = 'http://' . $siteUrl;
        }
        return rtrim(rtrim($siteUrl), '/');
    }

    protected function setCloudSearchWaiting()
    {
        $searchSetting = $this->getSettingService()->get('cloud_search', array());
        $searchSetting = array(
            'search_enabled' => 1,
            'status'         => 'waiting'
        );
        if (empty($searchSetting['type'])) {
            $searchSetting['type'] = array(
                'course'     => 1,
                'teacher'    => 1,
                'thread'     => 1, 
                'article'    => 1
            );
        }
        $this->getSettingService()->set('cloud_search', $searchSetting);
    }

    private function searchBase64Encode($conditions = array())
    {
        if (!empty($conditions['type'])) {
            $conditions['type'] = base64_encode($conditions['type']);
        }
        if (!empty($conditions['words'])) {
            $conditions['words'] = base64_encode($conditions['words']);
        }
        if (!empty($conditions['page'])) {
            $conditions['page'] = base64_encode($conditions['page']);
        }

        $conditions['method'] = 'base64';
        return $conditions;
    }

    protected function getSettingService()
    {
        return $this->createService('System.SettingService');
    }

    protected function getUserService()
    {
        return $this->createService('User.UserService');
    }

    protected function getSignEncoder()
    {
        return new MessageDigestPasswordEncoder('sha256');
    }
}
