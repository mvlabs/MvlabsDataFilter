<?php
namespace MVA\DataFilter;
/**
 * Manages pagination of results
 *
 * @author Stefano Valle <s.valle@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 */
class PaginateView {

// ------------------------------------------------------------------------------------------------------------------

	// If default records per page is not specified, we fall back to this
	const DEFAULT_RECORDS_PER_PAGE = 20;

	// Actual default records per page count will be assigned to this var upon initialization
	protected $m_defaultItemsPerPage;

	// Default items per page range; can be overriden
	protected $am_defaultItemsPerPageRange = array(10 => '10', // Strings are used so to allow custom labels
	                                               20 => '20', // IE for null delimiters
	                                               50 => '50');

	// Actions to invoke
	private $s_itemPerPageAction = 'perpage';
	private $s_paginateAction = 'paginate';

	private $i_currentPage;

	private $i_numPages;

	private $i_resultsCount;

	private $am_ordering;

	private $i_itemPerPage;

	public function __construct($i_resultsCount, $m_itemsPerPage = self::DEFAULT_RECORDS_PER_PAGE, $i_currentPage = 1, $am_ordering = array(),
	                            array $am_validItemsPerPage = null, $m_defaultRecordsPerPage = null) {

		// Important for this to be called before items per page and current page are returned
		$this->setDefaults($am_validItemsPerPage, $m_defaultRecordsPerPage);
		$this->setResultCount($i_resultsCount);
		$this->setItemsPerPage($m_itemsPerPage);
		$this->setCurrentPage($i_currentPage);
		$this->setOrdering($am_ordering);
	}

	public function setDefaults(array $am_validItemsPerPage = null, $m_defaultRecordsPerPage = null) {

		// Both needs to be taken into consideration; one can't exist on its own
		if (null !== $am_validItemsPerPage && null !== $m_defaultRecordsPerPage ) {
			if (!array_key_exists( $m_defaultRecordsPerPage, $am_validItemsPerPage)) {
				throw new \MVA\DataFilter\Exception('An invalid item per page number has been specified. Such must be among values in the range.');
			}
			$this->am_defaultItemsPerPageRange = $am_validItemsPerPage;
			$this->m_defaultItemsPerPage = $m_defaultRecordsPerPage;
			return true;
		}

		// If only one
		if ((null !== $am_validItemsPerPage || null !== $m_defaultRecordsPerPage) &&
			(null === $am_validItemsPerPage || null === $m_defaultRecordsPerPage)) {
			throw new \MVA\DataFilter\Exception('Can Not Specify a Valid Items per Page Range without a Default. Or the other way round.');
		}

		// If none has been specified, we simply fallback to class default...
		$this->m_defaultItemsPerPage = self::DEFAULT_RECORDS_PER_PAGE;
		return true;

	}

	public function getItemsPerPageRange() {
			return $this->am_defaultItemsPerPageRange;
	}

// ------------------------------------------------------------------------------------------------------------------

	public function setItemsPerPage($m_itemsPerPage)
	{

		// There's not user-defined items per page number
		//if (Selectioncriteria::DEFAULT_LIMIT  == $m_itemsPerPage) {
		//	$m_itemsPerPage = $this->m_defaultItemsPerPage;
		//} else {
			if (!in_array( $m_itemsPerPage, $this->am_defaultItemsPerPageRange)) {
				throw new \MVA\DataFilter\Exception('An invalid item per page number has been specified. Such must be among values in the range.');
			}
		//}
		$this->i_itemPerPage = $m_itemsPerPage;
	}

	public function getItemsPerPage()
	{
		return $this->i_itemPerPage;
	}

// ------------------------------------------------------------------------------------------------------------------

	public function setItemPerPageAction($s_paginateAction)
	{
		$this->s_paginateAction = $s_paginateAction;
	}

	public function getItemPerPageAction()
	{
		return $this->s_itemPerPageAction;
	}


// ------------------------------------------------------------------------------------------------------------------

	public function setResultCount($i_resultsCount)
	{
		$this->i_resultsCount = $i_resultsCount;
	}

	public function getResultCount()
	{
		return $this->i_resultsCount;
	}

// ------------------------------------------------------------------------------------------------------------------

	public function getNumPages()
	{
		if (0 != $this->i_itemPerPage) {
			return $this->i_numPages = ceil($this->i_resultsCount / $this->i_itemPerPage);
		}

		return 1;

	}

// ------------------------------------------------------------------------------------------------------------------

	public function setCurrentPage($i_currentPage)
	{
		$this->i_currentPage = (int) $i_currentPage;
		if (!$this->i_currentPage) $this->i_currentPage = 1;
		if ($this->i_currentPage > $this->i_numPages) $this->i_currentPage = $this->i_currentPage;
	}

	public function getCurrentPage()
	{
		return $this->i_currentPage;
	}

// ------------------------------------------------------------------------------------------------------------------

	public function getPaginateAction()
	{
		return $this->s_paginateAction;
	}

// ------------------------------------------------------------------------------------------------------------------
	public function setOrdering($am_ordering)
	{
		$this->am_ordering = $am_ordering;
	}

// ------------------------------------------------------------------------------------------------------------------

	public function getOrdering()
	{
		return $this->am_ordering;
	}

// ------------------------------------------------------------------------------------------------------------------

	public static function getOffset($i_currentPage, $m_itemsPerPage) {
		if (empty($i_currentPage) || !is_numeric($i_currentPage)) {
			$i_currentPage = 1;
		}
		return ($i_currentPage - 1) * $m_itemsPerPage;
	}

}