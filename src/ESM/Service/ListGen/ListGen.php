<?php

namespace App\ESM\Service\ListGen;

use App\ESM\Service\Utils\StringOrClosureTrait;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ListGenService.
 */
class ListGen
{
    use StringOrClosureTrait;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $exportFileName = '';

    /**
     * @var string[]
     */
    private $actionLabel = ['action.label', 'ListGen'];

    /**
     * @var Security
     */
    private $security;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var string
     */
    private $transDomain = 'ListGen';

    /**
     * @var string
     */
    private $id = '';

    /**
     * @var string
     */
    private $class = '';

    /**
     * @var \Closure|string
     */
    private $rowClass = '';

    /**
     * @var int
     */
    private $nbPerPage = 20;

    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var string
     */
    private $ajaxUrl;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var string
     */
    private $repositoryMethod;

    /**
     * @var array
     */
    private $repositoryArgs;

    /**
     * @var Closure|string
     */
    private $actionsClass;

    /**
     * @var array
     */
    private $nbPerPageOptions = [10, 20, 50, 100];

    /**
     * @var int
     * var for debug
     */
    private $nCall = 1;

    /**
     * @var bool
     */
    private $displayToolbar = true;

    /**
     * @var array|null
     */
    private $data = null;

    /**
     * @var Column[]
     */
    private $columns = [];

    /**
     * @var ColumnHidden[]
     */
    private $hiddenData = [];

    /**
     * @var Action[]
     */
    private $actions = [];

    /**
     * @var ActionMulti[]
     */
    private $multiActions = [];

    /**
     * @var array
     */
    private $rowData = [];

    /**
     * @var AbstractFilterType[]
     */
    private $filters = [];

    /**
     * @var array
     */
    private $sorts = [];

    /**
     * @var array
     */
    private $constantSort = [];

    /**
     * @var int
     */
    private $total = 0;

    /**
     * @var int
     */
    private $filteredTotal = 0;

    /**
     * ListGenService constructor.
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, Security $security)
    {
        $this->trans = $translator;
        $this->em = $entityManager;
        $this->security = $security;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return $this
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return array
     */
    public function getActionLabel(): array
    {
        return $this->actionLabel;
    }

    /**
     * @param string $actionLabel
     * @param string $domain
     */
    public function setActionLabel(string $actionLabel, string $domain = 'messages'): void
    {
        $this->actionLabel = [$actionLabel, $domain];
    }

    /**
     * @return int
     */
    public function getNbPerPage(): int
    {
        if (in_array($this->nbPerPage, $this->nbPerPageOptions)) {
            return $this->nbPerPage;
        } else {
            return $this->nbPerPageOptions[0];
        }
    }

    /**
     * @return $this
     */
    public function setNbPerPage(int $nbPerPage): self
    {
        $this->nbPerPage = $nbPerPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return $this
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return $this->ajaxUrl;
    }

    /**
     * @return $this
     */
    public function setAjaxUrl(string $ajaxUrl): self
    {
        $this->ajaxUrl = $ajaxUrl;

        return $this;
    }

    /**
     * @return ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->repository;
    }

    /**
     * @return $this
     */
    public function setRepository(ObjectRepository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setExportFileName(string $name): self
    {
        $this->exportFileName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepositoryMethod(): string
    {
        return $this->repositoryMethod;
    }

    /**
     * @param string $repositoryMethod
     * @param array $args
     * @return $this
     */
    public function setRepositoryMethod(string $repositoryMethod, array $args = []): self
    {
        $this->repositoryMethod = $repositoryMethod;
        $this->repositoryArgs = $args;

        return $this;
    }

    /**
     * @param Closure|string $class
     */
    public function setRowClass($class): self
    {
        $this->rowClass = $class;

        return $this;
    }

    /**
     * @param array $row
     * @return string
     */
    public function getRowClass(array $row): string
    {
        return $this->stringOrClosure($this->rowClass, [$row]);
    }

    /**
     * data-key => \Closure f($row) or string $field.
     */
    public function setRowData(array $data): self
    {
        $this->rowData = $data;

        return $this;
    }

    /**
     * @param \Closure|string $class
     */
    public function setActionsClass($class): self
    {
        $this->actionsClass = $class;

        return $this;
    }

    /**
     * @param array $row
     * @return string
     */
    public function getActionsClass(array $row): string
    {
        return $this->stringOrClosure($this->actionsClass, [$row]);
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function addColumn(array $settings): self
    {
        $column = new Column($settings);
        $this->columns[] = $column;
        $this->sorts[] = new Sort([
            'field' => $column->getSortField(),
            'order' => '',
            'priority' => 0,
        ]);

        return $this;
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function addAction(array $settings): self
    {
        $action = new Action($settings, $this->security);
        $action->addDataAttribute('actionpos', (string) count($this->actions));
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function addMultiAction(array $settings): self
    {
        $action = new ActionMulti($settings, $this->security);
        $action->addDataAttribute('multiactionpos', (string) count($this->multiActions));
        $this->multiActions[] = $action;

        return $this;
    }

    /**
     * @param array $settings
     * @param string $input
     * @return $this
     */
    public function addFilter(array $settings, string $input = 'string'): self
    {
        $settings['name'] = 'filter_'.count($this->filters);
        if ('string' == $input) {
            $filter = new FilterString($settings);
        } elseif ('select' == $input) {
            $filter = new FilterSelect($settings);
        } elseif ('archived' == $input) {
            $filter = new FilterArchived($settings);
        } elseif ('bool' == $input) {
            $filter = new FilterBool($settings);
        } elseif ('dates' == $input) {
            $filter = new FilterDates($settings);
        } else {
            return $this;
        }
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function addHiddenData(array $settings): self
    {
        $column = new ColumnHidden($settings);
        $this->hiddenData[] = $column;

        return $this;
    }

    /**
     * @return Response
     */
    public function generateResponse(): Response
    {
        if (-1 === $this->nbPerPage) {
            if (null == $this->data) {
                $this->setData();
            }

            $filename = date('Ymd_Hm_').urlencode($this->exportFileName).'.csv';

            // body
            $res = new StreamedResponse(function () {
                $delim = ';';
                $f = fopen('php://output', 'wb');
                // BOM
                fwrite($f, "\xEF\xBB\xBF");
                flush();

                // header
                fputcsv($f, $this->renderHeaderCsv(), $delim);
                flush();

                foreach ($this->data as $row) {
                    fputcsv($f, $this->renderRowCsv($row), $delim);
                    flush();
                }
                fclose($f);
            });
            $res->headers->set('Content-Type', 'text/csv');
            $res->headers->set('Content-Disposition', 'attachment; filename='.$filename);

            return $res;
        } else {
            $res = new Response($this->render(true));
            $res->headers->set('Content-Type', 'application/json');

            return $res;
        }
    }

    /**
     * @param array $row
     * @return array
     */
    public function renderRowCsv(array $row): array
    {
        $arr = [];
        foreach ($this->columns as $column) {
            $arr[] = $column->renderCsv($row);
        }

        return $arr;
    }

    /**
     * @param array $row
     * @return string
     */
    public function renderRow(array $row): string
    {
        $i = 0;
        $html = '';
        foreach ($this->columns as $column) {
            $displayCheckbox = 0 === $i && $this->displayRowCheckbox($row);
            $html .= $column->render($row, $displayCheckbox);
            ++$i;
        }
        if (count($this->actions) > 0) {
            $html .= $this->renderActions($row);
        }

        return $html;
    }

    /**
     * @return string
     */
    public function renderPagination(): string
    {
        $html = '';
        if ($this->nbPerPage > -1) {
            $totalPage = ceil($this->filteredTotal / $this->nbPerPage);
            $currentPage = $this->page;

            //pagination
            $html .= '<label>'.$this->trans->trans('toolbar.page', [], $this->transDomain).' <select name="page" class="select2">';
            $i = 1;
            while ($i <= $totalPage) {
                $html .= '<option value="'.$i.'"'.($i == $currentPage ? ' selected="selected"' : '').'>'.$i.'</option>';
                ++$i;
            }
            $html .= '</select> '.$this->trans->trans('toolbar.of', [], $this->transDomain).' '.$totalPage.' </label>';
        }

        return $html;
    }

    /**
     * @return string
     * todo renvoyer en ajax pour maj.
     */
    public function renderNbInfos(): string
    {
        $html = '<i>'.$this->trans->trans('toolbar.total', [], $this->transDomain).': '.$this->total.' - '.$this->trans->trans('toolbar.total_filtered', [], $this->transDomain).': '.$this->filteredTotal.'</i>';

        return $html;
    }

	/**
	 * @param bool $ajax
	 * @param string|null $nodeID
	 * @return string
	 */
    public function render(bool $ajax = false, string $nodeID = null): string
    {
        if (null == $this->data) {
            $this->setData();
        }
        if ($ajax) {
            return json_encode([
                'params' => $this->getJsonParams(),
                'html' => [
                    'table' => $this->renderTBody(),
                    //'toolbar-top' => $this->renderToolBarTop(), todo gérer placement pagination paramétrable
                    'toolbar-bottom' => $this->renderToolBarBottom(),
                ],
            ]);
        } else {
            // wrapper
            $html = '<div class="lg-table-wrap">';

            // toolbar top
            if ($this->displayToolbar) {
                $html .= '<div class="toolbar-top">';
                $html .= $this->renderToolBarTop($nodeID);
                $html .= '</div>';
            }

            // table
            $html .= '<div class="table-wrap table-responsive">';
            $html .= $this->renderTable();

            $html .= '</div>';

            // toolbar botoom
            if ($this->displayToolbar) {
                $html .= '<div class="toolbar-bottom text-center">';
                $html .= $this->renderToolBarBottom();
                $html .= '</div>';
            }

            $html .= '</div>';

            return $html;
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if (null == $this->data) {
            $this->setData();
        }

        return $this->data;
    }

    /**
     * @param array $filters
     */
    public function setDataAjax(array $filters): void
    {
        $qb = call_user_func_array([$this->repository, $this->repositoryMethod], $this->repositoryArgs);

        // on ajoute les select
        $i = 0;
        $arrTmp = array_merge($this->hiddenData, $this->columns);
        foreach ($arrTmp as $column) {
            if ('' != $column->getField()) {
                if ('' == $column->getAlias()) {
                    $field = $column->getField();
                } else {
                    $field = $column->getField().' AS '.$column->getAlias();
                }
                if (0 == $i) {
                    $qb->select($field);
                } else {
                    $qb->addSelect($field);
                }
                ++$i;
            }
        }

        // on filtre
        $i = 0;
        foreach ($filters as $key => $val) {
            /*if($i == 0){
                $qb->where($key . ' = :p'.$i);
            }else{*/
            $qb->andWhere($key.' = :p'.$i);
            //}
            $qb->setParameter(':p'.$i, $val);
            ++$i;
        }

        // on enlève les suffixes des noms de colonnes pour twig ou on garde les alias
        $this->removeSuffix();

        // on requête
        $query = $qb->getQuery();
        $this->data = $query->execute();
    }

    /**
     * @return array
     */
    public function getJsonParams(): array
    {
        // sorts
        $arrSort = [];
        foreach ($this->sorts as $sort) {
            $arrSort[] = [
                'order' => $sort->getOrder(),
                'priority' => $sort->getPriority(),
            ];
        }

        return [
            'nCall' => $this->nCall,
            'page' => $this->page,
            'nbPerPage' => $this->nbPerPage,
            //'filters' => $this->filters,
            'sorts' => $arrSort,
            'ajaxUrl' => $this->ajaxUrl,
            'total' => $this->total,
            'totalFiltered' => $this->filteredTotal,
        ];
    }

    /**
     * @param ParameterBag $request
     */
    public function setRequestParams(ParameterBag $request): void
    {
        if ('export' !== $request->get('trigger', '')) {
            $this->nCall = ($request->get('nCall') + 1);
            $this->nbPerPage = $request->get('nbPerPage');
            $this->page = $request->get('page');
        } else {
            $this->nbPerPage = -1;
        }

        // filters/sort
        if ('reset' != $request->get('trigger', '')) {
            // sorts
            $sortsParam = $request->get('sorts', null);
            if (!is_null($sortsParam)) {
                $i = 0;
                foreach ($sortsParam as $sort) {
                    if (is_string($sort)) {
                        $sort = json_decode($sort, true);
                    }
                    $this->sorts[$i]->setOrder($sort['order']);
                    $this->sorts[$i]->setPriority($sort['priority']);
                    ++$i;
                }
            }
            // filters
            $filtersParam = $request->get('filters', null);
            if (!is_null($filtersParam)) {
                $c = count($this->filters);
                for ($i = 0; $i < $c; ++$i) {
                    if (isset($filtersParam['filter_'.$i])) {
                        $values = $filtersParam['filter_'.$i];
                        if (is_array($values)) {
                            $this->filters[$i]->setValues($values);
                        } else {
                            $this->filters[$i]->setValues([$values]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $nbPerPageOptions
     * @return $this
     */
    public function setNbPerPageOptions(array $nbPerPageOptions): self
    {
        $this->nbPerPageOptions = $nbPerPageOptions;

        return $this;
    }

    /**
     * @return array|int[]
     */
    public function getNbPerPageOptions(): array
    {
        return $this->nbPerPageOptions;
    }

    /**
     * @param string $field
     * @param string $order
     * @param int $priority
     * @return $this
     */
    public function addConstantSort(string $field, string $order, int $priority = 0): self
    {
        $this->constantSort[] = new Sort([
            'field' => $field,
            'order' => $order,
            'priority' => $priority,
        ]);

        return $this;
    }

    /**
     * @param bool $display
     * @return $this
     */
    public function setDisplayToolbar(bool $display): self
    {
        $this->displayToolbar = $display;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDisplayToolbar(): bool
    {
        return $this->displayToolbar;
    }

    /**
     * @param array $filters
     * @return string
     */
    public function renderRowAjax(array $filters): string
    {
        $this->setDataAjax($filters);

        return $this->renderRow($this->data[0]);
    }

    /**
     * @param int $pos
     * @return array
     */
    public function getAfterAffectAjax(int $pos): array
    {
        $action = $this->actions[$pos];

        return $action->getAfterUpdEffect();
    }

    /**
     * @param array $row
     * @return string
     */
    private function renderActions(array $row): string
    {
        $html = $this->renderHtmlElement('td', [
            'class' => 'lg-action '.$this->getActionsClass($row),
        ]);
        foreach ($this->actions as $action) {
            $html .= $action->render($row).' ';
        }
        $html .= '</td>';

        return $html;
    }

    /**
     * @return string
     */
    private function renderTable(): string
    {
        $html = $this->renderHtmlElement('table', [
            'id' => $this->getId(),
            'class' => $this->getClass(),
            'data-params' => htmlspecialchars(json_encode($this->getJsonParams())),
        ]);
        $html .= '<thead>'.$this->renderTHead().'</thead>';
        $html .= '<tbody>'.$this->renderTBody().'</tbody>';
        $html .= '<tfoot>'.$this->renderTFooter().'</tfoot>';
        $html .= '</table>';

        return $html;
    }

    /**
     * on enlève les suffixes des noms de colonnes pour twig ou on garde les alias.
     */
    private function removeSuffix(): void
    {
        $i = 0;
        while ($i < count($this->columns)) {
            if ('' == $this->columns[$i]->getAlias()) {
                $arr = explode('.', $this->columns[$i]->getField());
                $this->columns[$i]->setAlias(end($arr));
            }
            ++$i;
        }
    }

    /**
     * do the query.
     */
    private function setData(): void
    {
        // on va chercher le query builder
        $qb = call_user_func_array([$this->repository, $this->repositoryMethod], $this->repositoryArgs);

        // total count todo optimize
        if (0 == $this->total) {
            $qb->select('1');
            $this->total = count($qb->getQuery()->getArrayResult());
        }

        // filters
        $iParam = 0; // incrémenter pour unicité :param
        foreach ($this->filters as $filter) {
            $wheres = $filter->getWhereQuery();
            foreach ($wheres as $where) {
                if ('' != $where['field']) {
                    $arr = [];
                    foreach ($where['values'] as $value) {
                        if ('' != $value && '%%' != $value && 'NULL' != $value) {
                            $arr[] = ':p'.$iParam;
                            $qb->setParameter(':p'.$iParam, $value);
                            ++$iParam;
                        }
                    }
                    if ('IS' == $where['op'] || 'IS NOT' == $where['op']) {
                        $qb->andWhere($where['field'].' '.$where['op'].' '.$where['values'][0]);
                    } else {
                        if (!empty($arr)) {
                            if ('IN' == $where['op']) {
                                $qb->andWhere($where['field'].' '.$where['op'].' ('.implode(',', $arr).')');
                            } else {
                                $qb->andWhere($where['field'].' '.$where['op'].' '.implode(',', $arr));
                            }
                        }
                    }
                }
            }
        }

        // filter count todo optimize
        if ($iParam > 0) {
            $qb->select('1');
            $this->filteredTotal = count($qb->getQuery()->getArrayResult());
        } else {
            $this->filteredTotal = $this->total;
        }

        // get filter select options (label/value)
        $qb->distinct(true);
        foreach ($this->filters as $filter) {
            if (FilterSelect::class === get_class($filter)) {
                /*
                 * @var FilterSelect $filter
                 */
                $qb->select($filter->getField());
                $qb->orderBy($filter->getField());

                if ((!is_object($filter->getSelectLabel()) || 'Closure' !== get_class($filter->getSelectLabel())) && '' != $filter->getSelectLabel()) {
                    $qb->addSelect($filter->getSelectLabel());
                    $qb->orderBy($filter->getSelectLabel());
                }

                $filter->setOptionsList($qb->getQuery()->getArrayResult());
            }
        }
        $qb->distinct(false);

        // on ajoute les select
        $i = 0;
        $arrTmp = array_merge($this->hiddenData, $this->columns);
        foreach ($arrTmp as $column) {
            if ('' != $column->getField()) {
                if ('' == $column->getAlias()) {
                    $field = $column->getField();
                } else {
                    $field = $column->getField().' AS '.$column->getAlias();
                }
                if (0 == $i) {
                    $qb->addSelect($field);
                } else {
                    $qb->addSelect($field);
                }
                ++$i;
            }
        }

        // limit
        if ($this->nbPerPage > -1) {
            $qb->setFirstResult(($this->page - 1) * $this->nbPerPage)
                ->setMaxResults($this->nbPerPage)
            ;
        }

        // sorts
        $iCol = 0;
        $arr = [];
        foreach ($this->sorts as $sort) {
            if (true == $this->columns[$iCol]->isSortable()) {
                if ('' != $sort->getOrder()) {
                    $arr[] = ['column' => $sort->getField(), 'order' => $sort->getOrder(), 'priority' => $sort->getPriority()];
                }
            }
            ++$iCol;
        }
        foreach ($this->constantSort as $sort) {
            if ('' != $sort->getOrder()) {
                $arr[] = ['column' => $sort->getField(), 'order' => $sort->getOrder(), 'priority' => 100 + $sort->getPriority()];
            }
        }
        // on tri par priorité 1,2,3,...
        usort($arr, function ($a, $b) {
            if ($a['priority'] == $b['priority']) {
                return 0;
            }

            return ($a['priority'] < $b['priority']) ? -1 : 1;
        });

        $i = 0;
        foreach ($arr as $sort) {
            if (0 == $i) {
                $qb->OrderBy($sort['column'], $sort['order']);
            } else {
                $qb->addOrderBy($sort['column'], $sort['order']);
            }
            ++$i;
        }

        // on enlève les suffixes des noms de colonnes pour twig ou on garde les alias
        $this->removeSuffix();

        // on requête
        $query = $qb->getQuery();
        $this->data = $query->execute();
    }

    /**
     * @return bool
     */
    private function hasMultiActions(): bool
    {
        foreach ($this->multiActions as $multiAction) {
            if ($multiAction->hasToBeDisplayed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $row
     * @return bool
     */
    private function displayRowCheckbox($row): bool
    {
        foreach ($this->multiActions as $multiAction) {
            if ($multiAction->hasToRenderCheckBox($row)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    private function renderHeaderCsv(): array
    {
        $arr = [];
        foreach ($this->columns as $column) {
            if (false !== $column->getTranslationDomain()) {
                $arr[] = $this->trans->trans($column->getLabel(), $column->getTranslationArgs(), $column->getTranslationDomain());
            } else {
                $arr[] = $column->getLabel();
            }
        }

        return $arr;
    }

    /**
     * @return string
     */
    private function renderTHead(): string
    {
        $html = '<tr>';
        $i = 0;
        foreach ($this->columns as $column) {
            $html .= '<th>';

            if (0 === $i && $this->hasMultiActions()) {
                $html .= '<input autocomplete="off" type="checkbox" /> ';
            }

            if (false !== $column->getTranslationDomain()) {
                $html .= $this->trans->trans($column->getLabel(), $column->getTranslationArgs(), $column->getTranslationDomain());
            } else {
                $html .= $column->getLabel();
            }
            if ($column->isSortable()) {
                $html .= ' <a class="lg-sort" href="'.$this->getAjaxUrl().'"><i class="fa fa-sort'.('ASC' == $this->sorts[$i]->getOrder() ? '-down' : ('DESC' == $this->sorts[$i]->getOrder() ? '-up' : '')).'"></i></a>';
            }
            $html .= '</th>';
            ++$i;
        }
        if (count($this->actions) > 0) {
            $html .= '<th>'.$this->trans->trans($this->actionLabel[0], ['%count%' => count($this->actions)], $this->actionLabel[1]).'</th>';
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * @return string
     */
    private function renderTBody(): string
    {
        $html = '';
        if (count($this->data) > 0) {
            foreach ($this->data as $row) {
                $attributes = [
                    'class' => $this->getRowClass($row),
                ];

                foreach ($this->rowData as $key => $val) {
                    $attributes['data-'.$key] = $this->stringOrClosure($val, [$row], function ($val, $row) {
                        return $row[$val];
                    });
                }
                $html .= $this->renderHtmlElement('tr', $attributes);
                $html .= $this->renderRow($row);
                $html .= '</tr>';
            }
            $html .= '';
        } else {
            $html .= '<tr><td colspan="'.count($this->columns).'"><i>'.$this->trans->trans('data.no_data', [], $this->transDomain).'</i></td></tr>';
        }

        return $html;
    }

    /**
     * @return string
     */
    private function renderTFooter(): string
    {
        $html = '';
        $html .= '';

        return $html;
    }

	/**
	 * @param null $nodeID
	 * @return string
	 */
    private function renderToolBarTop($nodeID = null): string
    {
    	$paramsRender = [
			'action' => $this->getAjaxUrl(),
			'method' => 'GET',
			'class'  => 'row',
		];

		if ($nodeID) {
			$paramsRender['id'] = $nodeID;
		}

        $html = $this->renderHtmlElement('form', $paramsRender);

        foreach ($this->filters as $filter) {
            $label = $filter->getLabel();
            if (false !== $filter->getTranslationDomain()) {
                $label = $this->trans->trans($filter->getLabel(), $filter->getTranslationArgs(), $filter->getTranslationDomain());
            }
            $html .= $filter->render($label, $this->trans);
        }
        if (count($this->filters) > 0) {
            $html .= '<div class="form-group col-lg-3"><button type="submit" name="submit" value="1" class="btn btn-primary">'.$this->trans->trans('toolbar.submit', [], $this->transDomain).'</button>';
            $html .= '<button type="submit" name="reset" value="1" class="btn btn-primary">'.$this->trans->trans('toolbar.reset', [], $this->transDomain).'</button></div>';
        }
        if ($this->hasMultiActions()) {
            $html .= '<div class="form-group col-lg-3 form-group"><select id="lg-actions" class="form-control"><option value="">Action</option>';
            foreach ($this->multiActions as $multiAction) {
                if ($multiAction->hasToBeDisplayed()) {
                    $html .= $multiAction->renderMultiAction();
                }
            }
            $html .= '</select></div>';
        }
        $html .= '<div class="form-group col-lg-3"><a href="javascript:void(0)" data-lg-export="" target="_blank" class="btn btn-primary">'.$this->trans->trans('toolbar.export', [], $this->transDomain).'</a></div>';
        $html .= '</form>';

        return $html;
    }

    /**
     * @return string
     */
    private function renderToolBarBottom(): string
    {
        $html = '<div class="row">';
        $html .= '<div class="col-lg-4 nb-per-page ">'.$this->renderNbPerPage().'</div>';
        $html .= '<div class="col-lg-4 nb-infos">'.$this->renderNbInfos().'</div>';
        $html .= '<div class="col-lg-4 page">'.$this->renderPagination().'</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @return string
     */
    private function renderNbPerPage(): string
    {
        $html = '';
        if ($this->nbPerPage > -1) {
            $html .= '<label>'.$this->trans->trans('toolbar.show', [], $this->transDomain).' <select name="show" class="select2">';
            foreach ($this->nbPerPageOptions as $option) {
                $html .= '<option value="'.$option.'"'.($this->nbPerPage == $option ? ' selected="selected"' : '').'>'.$option.'</option>';
            }
            $html .= '</select></label>';
        }
        $html .= '';

        return $html;
    }
}
