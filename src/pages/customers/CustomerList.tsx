import React, { useState, useEffect } from 'react';
import {
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Paper,
    Button,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    TextField,
    IconButton,
    Typography,
    Box,
    Pagination,
    CircularProgress,
    Alert,
    InputAdornment,
    MenuItem,
    FormControl,
    InputLabel,
    Select,
    Checkbox,
    FormControlLabel,
    Divider,
    Card,
    CardContent,
    Tabs,
    Tab,
    AppBar,
    Toolbar,
    Grid,
    Chip
} from '@mui/material';
import { 
    Add as AddIcon, 
    Edit as EditIcon, 
    Delete as DeleteIcon, 
    Search as SearchIcon,
    Person as PersonIcon,
    Accessible as AccessibleIcon,
    Phone as PhoneIcon,
    Home as HomeIcon,
    Add as Add
} from '@mui/icons-material';
import { format } from 'date-fns';

interface Customer {
    customer_id: number;
    name: string;
    birth_date: string | null;
    gender: string | null;
    phone: string | null;
    address: string | null;
    created_at: string | null;
    updated_at: string | null;
    care_number: string | null;
    care_type: string | null;
    burden_rate: string | null;
    grade: string | null;
    care_start_date: string | null;
    care_end_date: string | null;
    guardian_name: string | null;
    guardian_relation: string | null;
    guardian_phone: string | null;
    notes: string | null;
}

interface CustomerFormData {
    name: string;
    birth_date: string;
    gender: string;
    phone: string;
    address: string;
    is_care_customer: boolean;
    care_number: string;
    care_type: string;
    burden_rate: string;
    grade: string;
    care_start_date: string;
    care_end_date: string;
    guardian_name: string;
    guardian_relation: string;
    guardian_phone: string;
    notes: string;
}

const CustomerList: React.FC = () => {
    const [customers, setCustomers] = useState<Customer[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [page, setPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [searchTerm, setSearchTerm] = useState('');
    const [openForm, setOpenForm] = useState(false);
    const [openDelete, setOpenDelete] = useState(false);
    const [selectedCustomer, setSelectedCustomer] = useState<Customer | null>(null);
    const [formTab, setFormTab] = useState(0);
    const [customerTypeFilter, setCustomerTypeFilter] = useState<'all' | 'regular' | 'care'>('all');
    const [openDetailDialog, setOpenDetailDialog] = useState(false);
    const [detailTab, setDetailTab] = useState(0);
    const [formData, setFormData] = useState<CustomerFormData>({
        name: '',
        birth_date: '',
        gender: '',
        phone: '',
        address: '',
        is_care_customer: false,
        care_number: '',
        care_type: '',
        burden_rate: '',
        grade: '',
        care_start_date: '',
        care_end_date: '',
        guardian_name: '',
        guardian_relation: '',
        guardian_phone: '',
        notes: ''
    });

    // 복지용구 고객 및 일반 고객 수 계산
    const getCareCustomerCount = () => {
        return customers.filter(customer => customer.care_number).length;
    };

    const getRegularCustomerCount = () => {
        return customers.filter(customer => !customer.care_number).length;
    };

    // 필터링된 고객 목록 가져오기
    const getFilteredCustomers = () => {
        if (customerTypeFilter === 'all') {
            return customers;
        } else if (customerTypeFilter === 'care') {
            return customers.filter(customer => customer.care_number);
        } else {
            return customers.filter(customer => !customer.care_number);
        }
    };

    // 고객 필터 토글
    const toggleCustomerTypeFilter = (filterType: 'all' | 'regular' | 'care') => {
        if (customerTypeFilter === filterType) {
            setCustomerTypeFilter('all'); // 같은 필터를 다시 클릭하면 모든 고객 표시
        } else {
            setCustomerTypeFilter(filterType);
        }
        setPage(1); // 페이지를 첫 페이지로 리셋
    };

    const loadCustomers = async () => {
        try {
            setLoading(true);
            const response = await fetch(
                `http://localhost/caremall/api/customers/?page=${page}&search=${searchTerm}`
            );
            const data = await response.json();
            
            if (data.status === 'success') {
                setCustomers(data.data.customers);
                setTotalPages(data.data.total_pages);
            } else {
                setError(data.message);
            }
        } catch (err) {
            setError('고객 목록을 불러오는 중 오류가 발생했습니다.');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadCustomers();
    }, [page, searchTerm]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        
        // 필수 입력 값 검증
        if (!formData.name || !formData.phone) {
            setError('이름과 전화번호는 필수 입력 항목입니다.');
            return;
        }
        
        // 복지용구 고객 정보가 있는 경우 필수 항목 검증
        if (formData.is_care_customer) {
            // 복지용구 고객이라면 필수 정보 검증
            if (!formData.care_number) {
                setError('장기요양인정번호는 필수 입력 항목입니다.');
                return;
            }
            if (!formData.care_type) {
                setError('수급자 구분은 필수 입력 항목입니다.');
                return;
            }
            if (!formData.burden_rate) {
                setError('부담율은 필수 입력 항목입니다.');
                return;
            }
            if (!formData.grade) {
                setError('등급은 필수 입력 항목입니다.');
                return;
            }
            if (!formData.care_start_date || !formData.care_end_date) {
                setError('유효기간 시작일과 종료일은 필수 입력 항목입니다.');
                return;
            }
        }
        
        try {
            const url = selectedCustomer
                ? `http://localhost/caremall/api/customers/?id=${selectedCustomer.customer_id}`
                : 'http://localhost/caremall/api/customers/';
            
            setLoading(true);  // 저장 중 로딩 표시
            const response = await fetch(url, {
                method: selectedCustomer ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            const data = await response.json();
            if (data.status === 'success') {
                setOpenForm(false);
                loadCustomers();
                resetForm();
            } else {
                setError(data.message);
            }
        } catch (err) {
            setError('고객 정보 저장 중 오류가 발생했습니다.');
        } finally {
            setLoading(false);  // 로딩 표시 종료
        }
    };

    const handleDelete = async () => {
        if (!selectedCustomer) return;
        
        try {
            setLoading(true);  // 삭제 중 로딩 표시
            const response = await fetch(
                `http://localhost/caremall/api/customers/?id=${selectedCustomer.customer_id}`,
                { method: 'DELETE' }
            );

            const data = await response.json();
            if (data.status === 'success') {
                setOpenDelete(false);
                loadCustomers();
                setSelectedCustomer(null);
            } else {
                setError(data.message);
            }
        } catch (err) {
            setError('고객 삭제 중 오류가 발생했습니다.');
        } finally {
            setLoading(false);  // 로딩 표시 종료
        }
    };

    const resetForm = () => {
        setFormData({
            name: '',
            birth_date: '',
            gender: '',
            phone: '',
            address: '',
            is_care_customer: false,
            care_number: '',
            care_type: '',
            burden_rate: '',
            grade: '',
            care_start_date: '',
            care_end_date: '',
            guardian_name: '',
            guardian_relation: '',
            guardian_phone: '',
            notes: ''
        });
        setSelectedCustomer(null);
        setFormTab(0);
    };

    const handleEdit = (customer: Customer) => {
        setSelectedCustomer(customer);
        setFormData({
            name: customer.name,
            birth_date: customer.birth_date || '',
            gender: customer.gender || '',
            phone: customer.phone || '',
            address: customer.address || '',
            is_care_customer: !!customer.care_number,
            care_number: customer.care_number || '',
            care_type: customer.care_type || '',
            burden_rate: customer.burden_rate || '',
            grade: customer.grade || '',
            care_start_date: customer.care_start_date || '',
            care_end_date: customer.care_end_date || '',
            guardian_name: customer.guardian_name || '',
            guardian_relation: customer.guardian_relation || '',
            guardian_phone: customer.guardian_phone || '',
            notes: customer.notes || ''
        });
        setOpenForm(true);
    };

    // 고객 유형별 칩 색상 지정
    const getChipColor = (careNumber: string | null) => {
        return careNumber ? 'primary' : 'default';
    };

    // 고객 상세 정보 보기
    const handleViewDetail = (customer: Customer) => {
        setSelectedCustomer(customer);
        setOpenDetailDialog(true);
        setDetailTab(0); // 기본 정보 탭으로 초기화
    };

    return (
        <Box sx={{ p: 0 }}>
            {/* 상단 앱바 */}
            <AppBar position="static" color="primary" sx={{ mb: 3 }}>
                <Toolbar>
                    <Typography variant="h6" component="div" sx={{ flexGrow: 1 }}>
                        고객 관리
                    </Typography>
                    <Button 
                        color="inherit" 
                        startIcon={<AddIcon />}
                        onClick={() => {
                            resetForm();
                            setOpenForm(true);
                        }}
                    >
                        신규 고객 등록
                    </Button>
                </Toolbar>
            </AppBar>

            {error && (
                <Alert severity="error" sx={{ mb: 2, mx: 3 }} onClose={() => setError(null)}>
                    {error}
                </Alert>
            )}

            <Card sx={{ mx: 3, mb: 3 }}>
                <CardContent>
                    <Grid container spacing={2} alignItems="center">
                        <Grid item xs={12} md={6}>
                            <TextField
                                placeholder="고객 이름, 전화번호, 주소로 검색..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                fullWidth
                                InputProps={{
                                    startAdornment: (
                                        <InputAdornment position="start">
                                            <SearchIcon />
                                        </InputAdornment>
                                    ),
                                }}
                            />
                        </Grid>
                        <Grid item xs={12} md={6} sx={{ textAlign: 'right' }}>
                            <Box sx={{ display: 'flex', justifyContent: 'flex-end', alignItems: 'center', gap: 2 }}>
                                <Chip
                                    icon={<PersonIcon />}
                                    label={`일반 고객: ${getRegularCustomerCount()}명`}
                                    variant={customerTypeFilter === 'regular' ? "filled" : "outlined"}
                                    color={customerTypeFilter === 'regular' ? "primary" : "default"}
                                    size="small"
                                    onClick={() => toggleCustomerTypeFilter('regular')}
                                    sx={{ cursor: 'pointer' }}
                                />
                                <Chip
                                    icon={<AccessibleIcon />}
                                    label={`복지용구 고객: ${getCareCustomerCount()}명`}
                                    variant={customerTypeFilter === 'care' ? "filled" : "outlined"}
                                    color={customerTypeFilter === 'care' ? "primary" : "default"}
                                    size="small"
                                    onClick={() => toggleCustomerTypeFilter('care')}
                                    sx={{ cursor: 'pointer' }}
                                />
                                <Typography variant="body2" color="textSecondary" 
                                    onClick={() => setCustomerTypeFilter('all')}
                                    sx={{ 
                                        cursor: 'pointer', 
                                        textDecoration: customerTypeFilter === 'all' ? 'underline' : 'none',
                                        fontWeight: customerTypeFilter === 'all' ? 'bold' : 'normal'
                                    }}
                                >
                                    총 고객 수: {customers.length}명
                                </Typography>
                            </Box>
                        </Grid>
                    </Grid>
                </CardContent>
            </Card>

            {loading ? (
                <Box sx={{ display: 'flex', justifyContent: 'center', p: 3 }}>
                    <CircularProgress />
                </Box>
            ) : (
                <Box sx={{ mx: 3 }}>
                    <TableContainer component={Paper} sx={{ mb: 3 }}>
                        <Table>
                            <TableHead sx={{ backgroundColor: '#f5f5f5' }}>
                                <TableRow>
                                    <TableCell width={50}>ID</TableCell>
                                    <TableCell>이름</TableCell>
                                    <TableCell>
                                        유형
                                        {customerTypeFilter !== 'all' && (
                                            <Chip 
                                                label={customerTypeFilter === 'care' ? '복지용구만 표시' : '일반 고객만 표시'} 
                                                color="primary"
                                                size="small"
                                                onDelete={() => setCustomerTypeFilter('all')}
                                                sx={{ ml: 1 }}
                                            />
                                        )}
                                    </TableCell>
                                    <TableCell>생년월일</TableCell>
                                    <TableCell>성별</TableCell>
                                    <TableCell>전화번호</TableCell>
                                    <TableCell>주소</TableCell>
                                    <TableCell>장기요양번호</TableCell>
                                    <TableCell>등급</TableCell>
                                    <TableCell>관리</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {getFilteredCustomers().length > 0 ? (
                                    getFilteredCustomers().map((customer) => (
                                        <TableRow key={customer.customer_id} hover>
                                            <TableCell>{customer.customer_id}</TableCell>
                                            <TableCell 
                                                sx={{ 
                                                    fontWeight: 'bold', 
                                                    cursor: 'pointer',
                                                    '&:hover': {
                                                        color: 'primary.main',
                                                        textDecoration: 'underline'
                                                    }
                                                }}
                                                onClick={() => handleViewDetail(customer)}
                                            >
                                                {customer.name}
                                            </TableCell>
                                            <TableCell>
                                                <Chip 
                                                    label={customer.care_number ? "복지용구" : "일반"} 
                                                    color={getChipColor(customer.care_number)}
                                                    size="small"
                                                    icon={customer.care_number ? <AccessibleIcon /> : <PersonIcon />}
                                                />
                                            </TableCell>
                                            <TableCell>
                                                {customer.birth_date && 
                                                format(new Date(customer.birth_date), 'yyyy-MM-dd')}
                                            </TableCell>
                                            <TableCell>{customer.gender}</TableCell>
                                            <TableCell>
                                                <Box sx={{ display: 'flex', alignItems: 'center' }}>
                                                    <PhoneIcon fontSize="small" sx={{ mr: 0.5, color: 'text.secondary' }} />
                                                    {customer.phone}
                                                </Box>
                                            </TableCell>
                                            <TableCell>
                                                <Box sx={{ display: 'flex', alignItems: 'center' }}>
                                                    <HomeIcon fontSize="small" sx={{ mr: 0.5, color: 'text.secondary' }} />
                                                    {customer.address}
                                                </Box>
                                            </TableCell>
                                            <TableCell>{customer.care_number}</TableCell>
                                            <TableCell>{customer.grade}</TableCell>
                                            <TableCell>
                                                <IconButton
                                                    color="primary"
                                                    onClick={() => handleEdit(customer)}
                                                    size="small"
                                                >
                                                    <EditIcon />
                                                </IconButton>
                                                <IconButton
                                                    color="error"
                                                    onClick={() => {
                                                        setSelectedCustomer(customer);
                                                        setOpenDelete(true);
                                                    }}
                                                    size="small"
                                                >
                                                    <DeleteIcon />
                                                </IconButton>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={10} align="center" sx={{ py: 3 }}>
                                            {customerTypeFilter !== 'all' 
                                                ? `${customerTypeFilter === 'care' ? '복지용구' : '일반'} 고객 정보가 없습니다.`
                                                : '고객 정보가 없습니다.'}
                                            {searchTerm && (
                                                <Box sx={{ mt: 1 }}>
                                                    <Typography variant="body2" color="text.secondary">
                                                        검색어: "{searchTerm}"에 대한 결과가 없습니다.
                                                    </Typography>
                                                    <Button
                                                        size="small"
                                                        onClick={() => {
                                                            setSearchTerm('');
                                                            setCustomerTypeFilter('all');
                                                        }}
                                                        sx={{ mt: 1 }}
                                                    >
                                                        모든 고객 보기
                                                    </Button>
                                                </Box>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </TableContainer>

                    <Box sx={{ mt: 2, mb: 4, display: 'flex', justifyContent: 'center' }}>
                        <Pagination
                            count={totalPages}
                            page={page}
                            onChange={(_, value) => setPage(value)}
                            color="primary"
                            size="large"
                            showFirstButton
                            showLastButton
                        />
                    </Box>
                </Box>
            )}

            {/* 고객 추가/수정 폼 다이얼로그 */}
            <Dialog open={openForm} onClose={() => setOpenForm(false)} maxWidth="md" fullWidth>
                <form onSubmit={handleSubmit}>
                    <DialogTitle sx={{ pb: 0 }}>
                        <Box sx={{ borderBottom: 1, borderColor: 'divider' }}>
                            <Typography variant="h6" sx={{ mb: 2 }}>
                                {selectedCustomer ? '고객 정보 수정' : '신규 고객 등록'}
                            </Typography>
                            <Tabs value={formTab} onChange={(_, newValue) => setFormTab(newValue)}>
                                <Tab label="기본 정보" />
                                {formData.is_care_customer && <Tab label="수급자 정보" />}
                                {formData.is_care_customer && <Tab label="보호자 정보" />}
                                <Tab label="추가 정보" />
                            </Tabs>
                        </Box>
                    </DialogTitle>
                    <DialogContent>
                        <Box sx={{ mt: 2 }}>
                            <FormControlLabel
                                control={
                                    <Checkbox
                                        checked={formData.is_care_customer}
                                        onChange={(e) => setFormData({ ...formData, is_care_customer: e.target.checked })}
                                        name="is_care_customer"
                                    />
                                }
                                label={
                                    <Box sx={{ display: 'flex', alignItems: 'center' }}>
                                        <AccessibleIcon sx={{ mr: 1, color: 'primary.main' }} />
                                        <Typography variant="body1" sx={{ fontWeight: 'bold' }}>
                                            복지용구 고객
                                        </Typography>
                                    </Box>
                                }
                                sx={{ mb: 2 }}
                            />
                            
                            {/* 기본 정보 탭 */}
                            {formTab === 0 && (
                                <Grid container spacing={2}>
                                    <Grid item xs={12} md={6}>
                                        <TextField
                                            label="이름"
                                            value={formData.name}
                                            onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                                            fullWidth
                                            required
                                        />
                                    </Grid>
                                    <Grid item xs={12} md={6}>
                                        <TextField
                                            label="전화번호"
                                            value={formData.phone}
                                            onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                                            fullWidth
                                            required
                                        />
                                    </Grid>
                                    <Grid item xs={12}>
                                        <TextField
                                            label="주소"
                                            value={formData.address}
                                            onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                                            fullWidth
                                        />
                                    </Grid>
                                    <Grid item xs={12} md={6}>
                                        <TextField
                                            label="생년월일"
                                            type="date"
                                            value={formData.birth_date}
                                            onChange={(e) => setFormData({ ...formData, birth_date: e.target.value })}
                                            fullWidth
                                            InputLabelProps={{ shrink: true }}
                                        />
                                    </Grid>
                                    <Grid item xs={12} md={6}>
                                        <FormControl fullWidth>
                                            <InputLabel id="gender-label">성별</InputLabel>
                                            <Select
                                                labelId="gender-label"
                                                value={formData.gender}
                                                label="성별"
                                                onChange={(e) => setFormData({ ...formData, gender: e.target.value })}
                                            >
                                                <MenuItem value="남">남</MenuItem>
                                                <MenuItem value="여">여</MenuItem>
                                            </Select>
                                        </FormControl>
                                    </Grid>
                                </Grid>
                            )}
                            
                            {/* 수급자 정보 탭 */}
                            {formTab === 1 && formData.is_care_customer && (
                                <Grid container spacing={2}>
                                    <Grid item xs={12}>
                                        <TextField
                                            label="장기요양인정번호"
                                            value={formData.care_number}
                                            onChange={(e) => setFormData({ ...formData, care_number: e.target.value })}
                                            fullWidth
                                            required
                                        />
                                    </Grid>
                                    <Grid item xs={12} md={4}>
                                        <FormControl fullWidth required>
                                            <InputLabel id="care-type-label">수급자 구분</InputLabel>
                                            <Select
                                                labelId="care-type-label"
                                                value={formData.care_type}
                                                label="수급자 구분"
                                                onChange={(e) => setFormData({ ...formData, care_type: e.target.value })}
                                            >
                                                <MenuItem value="일반">일반</MenuItem>
                                                <MenuItem value="의료">의료</MenuItem>
                                            </Select>
                                        </FormControl>
                                    </Grid>
                                    <Grid item xs={12} md={4}>
                                        <FormControl fullWidth required>
                                            <InputLabel id="burden-rate-label">부담율</InputLabel>
                                            <Select
                                                labelId="burden-rate-label"
                                                value={formData.burden_rate}
                                                label="부담율"
                                                onChange={(e) => setFormData({ ...formData, burden_rate: e.target.value })}
                                            >
                                                <MenuItem value="0%">0%</MenuItem>
                                                <MenuItem value="6%">6%</MenuItem>
                                                <MenuItem value="9%">9%</MenuItem>
                                                <MenuItem value="15%">15%</MenuItem>
                                            </Select>
                                        </FormControl>
                                    </Grid>
                                    <Grid item xs={12} md={4}>
                                        <FormControl fullWidth required>
                                            <InputLabel id="grade-label">등급</InputLabel>
                                            <Select
                                                labelId="grade-label"
                                                value={formData.grade}
                                                label="등급"
                                                onChange={(e) => setFormData({ ...formData, grade: e.target.value })}
                                            >
                                                <MenuItem value="1등급">1등급</MenuItem>
                                                <MenuItem value="2등급">2등급</MenuItem>
                                                <MenuItem value="3등급">3등급</MenuItem>
                                                <MenuItem value="4등급">4등급</MenuItem>
                                                <MenuItem value="5등급">5등급</MenuItem>
                                                <MenuItem value="인지지원등급">인지지원등급</MenuItem>
                                            </Select>
                                        </FormControl>
                                    </Grid>
                                    <Grid item xs={12} md={6}>
                                        <TextField
                                            label="유효기간(시작일자)"
                                            type="date"
                                            value={formData.care_start_date}
                                            onChange={(e) => setFormData({ ...formData, care_start_date: e.target.value })}
                                            fullWidth
                                            required
                                            InputLabelProps={{ shrink: true }}
                                        />
                                    </Grid>
                                    <Grid item xs={12} md={6}>
                                        <TextField
                                            label="유효기간(종료일자)"
                                            type="date"
                                            value={formData.care_end_date}
                                            onChange={(e) => setFormData({ ...formData, care_end_date: e.target.value })}
                                            fullWidth
                                            required
                                            InputLabelProps={{ shrink: true }}
                                        />
                                    </Grid>
                                </Grid>
                            )}
                            
                            {/* 보호자 정보 탭 */}
                            {formTab === 2 && formData.is_care_customer && (
                                <Grid container spacing={2}>
                                    <Grid item xs={12} md={6}>
                                        <TextField
                                            label="보호자 이름"
                                            value={formData.guardian_name}
                                            onChange={(e) => setFormData({ ...formData, guardian_name: e.target.value })}
                                            fullWidth
                                        />
                                    </Grid>
                                    <Grid item xs={12} md={6}>
                                        <TextField
                                            label="보호자 전화번호"
                                            value={formData.guardian_phone}
                                            onChange={(e) => setFormData({ ...formData, guardian_phone: e.target.value })}
                                            fullWidth
                                        />
                                    </Grid>
                                    <Grid item xs={12}>
                                        <FormControl fullWidth>
                                            <InputLabel id="guardian-relation-label">보호자 관계</InputLabel>
                                            <Select
                                                labelId="guardian-relation-label"
                                                value={formData.guardian_relation}
                                                label="보호자 관계"
                                                onChange={(e) => setFormData({ ...formData, guardian_relation: e.target.value })}
                                            >
                                                <MenuItem value="배우자">배우자</MenuItem>
                                                <MenuItem value="자녀">자녀</MenuItem>
                                                <MenuItem value="자부">자부</MenuItem>
                                                <MenuItem value="사위">사위</MenuItem>
                                                <MenuItem value="친척">친척</MenuItem>
                                                <MenuItem value="사회복지공무원">사회복지공무원</MenuItem>
                                            </Select>
                                        </FormControl>
                                    </Grid>
                                </Grid>
                            )}
                            
                            {/* 추가 정보 탭 */}
                            {((formTab === 3 && formData.is_care_customer) || (formTab === 1 && !formData.is_care_customer)) && (
                                <Grid container spacing={2}>
                                    <Grid item xs={12}>
                                        <TextField
                                            label="비고"
                                            value={formData.notes}
                                            onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                                            fullWidth
                                            multiline
                                            rows={4}
                                        />
                                    </Grid>
                                </Grid>
                            )}
                        </Box>
                    </DialogContent>
                    <DialogActions sx={{ px: 3, pb: 3 }}>
                        <Button 
                            onClick={() => setOpenForm(false)} 
                            variant="outlined"
                        >
                            취소
                        </Button>
                        <Button 
                            type="submit" 
                            variant="contained" 
                            color="primary"
                            startIcon={selectedCustomer ? <EditIcon /> : <AddIcon />}
                        >
                            {selectedCustomer ? '수정 완료' : '등록 완료'}
                        </Button>
                    </DialogActions>
                </form>
            </Dialog>

            {/* 삭제 확인 다이얼로그 */}
            <Dialog open={openDelete} onClose={() => setOpenDelete(false)}>
                <DialogTitle>고객 삭제</DialogTitle>
                <DialogContent>
                    <Typography>
                        {selectedCustomer?.name} 고객을 삭제하시겠습니까?
                    </Typography>
                    <Typography color="error" variant="body2" sx={{ mt: 2 }}>
                        * 삭제된 데이터는 복구할 수 없습니다.
                    </Typography>
                </DialogContent>
                <DialogActions>
                    <Button onClick={() => setOpenDelete(false)}>취소</Button>
                    <Button onClick={handleDelete} color="error" variant="contained">
                        삭제
                    </Button>
                </DialogActions>
            </Dialog>

            {/* 고객 상세 정보 다이얼로그 */}
            <Dialog 
                open={openDetailDialog} 
                onClose={() => setOpenDetailDialog(false)}
                maxWidth="md"
                fullWidth
            >
                {selectedCustomer && (
                    <>
                        <DialogTitle sx={{ pb: 0 }}>
                            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 1 }}>
                                <Typography variant="h6">
                                    {selectedCustomer.name} 고객 상세 정보
                                    <Chip 
                                        label={selectedCustomer.care_number ? "복지용구" : "일반"} 
                                        color={getChipColor(selectedCustomer.care_number)}
                                        size="small"
                                        icon={selectedCustomer.care_number ? <AccessibleIcon /> : <PersonIcon />}
                                        sx={{ ml: 1, verticalAlign: 'middle' }}
                                    />
                                </Typography>
                                <IconButton onClick={() => handleEdit(selectedCustomer)} color="primary">
                                    <EditIcon />
                                </IconButton>
                            </Box>
                            <Tabs value={detailTab} onChange={(_, newValue) => setDetailTab(newValue)}>
                                <Tab label="기본 정보" />
                                {selectedCustomer.care_number && <Tab label="수급자 정보" />}
                                {selectedCustomer.care_number && <Tab label="보호자 정보" />}
                                <Tab label="구매 이력" />
                            </Tabs>
                        </DialogTitle>
                        <DialogContent dividers>
                            {/* 기본 정보 탭 */}
                            {detailTab === 0 && (
                                <Grid container spacing={2} sx={{ mt: 1 }}>
                                    <Grid item xs={12} md={6}>
                                        <Paper elevation={0} sx={{ p: 2, bgcolor: 'background.default' }}>
                                            <Box sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                                                <PersonIcon color="primary" sx={{ mr: 1 }} />
                                                <Typography variant="subtitle1" sx={{ fontWeight: 'bold' }}>
                                                    개인 정보
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '100px' }}>
                                                    고객 ID:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.customer_id}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '100px' }}>
                                                    이름:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.name}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '100px' }}>
                                                    생년월일:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.birth_date && 
                                                    format(new Date(selectedCustomer.birth_date), 'yyyy-MM-dd')}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '100px' }}>
                                                    성별:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.gender}
                                                </Typography>
                                            </Box>
                                        </Paper>
                                    </Grid>
                                    <Grid item xs={12} md={6}>
                                        <Paper elevation={0} sx={{ p: 2, bgcolor: 'background.default' }}>
                                            <Box sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                                                <PhoneIcon color="primary" sx={{ mr: 1 }} />
                                                <Typography variant="subtitle1" sx={{ fontWeight: 'bold' }}>
                                                    연락처 정보
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '100px' }}>
                                                    전화번호:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.phone}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '100px' }}>
                                                    주소:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.address}
                                                </Typography>
                                            </Box>
                                        </Paper>
                                    </Grid>
                                    <Grid item xs={12}>
                                        <Paper elevation={0} sx={{ p: 2, bgcolor: 'background.default' }}>
                                            <Box sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                                                <Typography variant="subtitle1" sx={{ fontWeight: 'bold' }}>
                                                    추가 정보
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '100px' }}>
                                                    등록일:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.created_at && 
                                                    format(new Date(selectedCustomer.created_at), 'yyyy-MM-dd')}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '100px' }}>
                                                    최근 수정일:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.updated_at && 
                                                    format(new Date(selectedCustomer.updated_at), 'yyyy-MM-dd')}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '100px' }}>
                                                    비고:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.notes || '없음'}
                                                </Typography>
                                            </Box>
                                        </Paper>
                                    </Grid>
                                </Grid>
                            )}

                            {/* 수급자 정보 탭 */}
                            {detailTab === 1 && selectedCustomer.care_number && (
                                <Grid container spacing={2} sx={{ mt: 1 }}>
                                    <Grid item xs={12}>
                                        <Paper elevation={0} sx={{ p: 2, bgcolor: 'background.default' }}>
                                            <Box sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                                                <AccessibleIcon color="primary" sx={{ mr: 1 }} />
                                                <Typography variant="subtitle1" sx={{ fontWeight: 'bold' }}>
                                                    장기요양보험 정보
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '180px' }}>
                                                    장기요양인정번호:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.care_number}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '180px' }}>
                                                    수급자 구분:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.care_type}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '180px' }}>
                                                    등급:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.grade}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '180px' }}>
                                                    부담율:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.burden_rate}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '180px' }}>
                                                    유효기간:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.care_start_date && 
                                                    format(new Date(selectedCustomer.care_start_date), 'yyyy-MM-dd')}
                                                    &nbsp;~&nbsp;
                                                    {selectedCustomer.care_end_date && 
                                                    format(new Date(selectedCustomer.care_end_date), 'yyyy-MM-dd')}
                                                </Typography>
                                            </Box>
                                        </Paper>
                                    </Grid>
                                </Grid>
                            )}

                            {/* 보호자 정보 탭 */}
                            {detailTab === 2 && selectedCustomer.care_number && (
                                <Grid container spacing={2} sx={{ mt: 1 }}>
                                    <Grid item xs={12}>
                                        <Paper elevation={0} sx={{ p: 2, bgcolor: 'background.default' }}>
                                            <Box sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                                                <PersonIcon color="primary" sx={{ mr: 1 }} />
                                                <Typography variant="subtitle1" sx={{ fontWeight: 'bold' }}>
                                                    보호자 정보
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '130px' }}>
                                                    보호자 이름:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.guardian_name || '정보 없음'}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '130px' }}>
                                                    보호자 관계:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.guardian_relation || '정보 없음'}
                                                </Typography>
                                            </Box>
                                            <Box sx={{ display: 'flex', mb: 1 }}>
                                                <Typography variant="body2" sx={{ fontWeight: 'bold', width: '130px' }}>
                                                    보호자 연락처:
                                                </Typography>
                                                <Typography variant="body2">
                                                    {selectedCustomer.guardian_phone || '정보 없음'}
                                                </Typography>
                                            </Box>
                                        </Paper>
                                    </Grid>
                                </Grid>
                            )}

                            {/* 구매 이력 탭 */}
                            {detailTab === 3 || (detailTab === 1 && !selectedCustomer.care_number) || (detailTab === 2 && !selectedCustomer.care_number) && (
                                <Box sx={{ mt: 2 }}>
                                    <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                                        <Typography variant="subtitle1" sx={{ fontWeight: 'bold' }}>
                                            구매 이력
                                        </Typography>
                                        <Button size="small" variant="outlined" startIcon={<Add />}>
                                            새 구매 추가
                                        </Button>
                                    </Box>
                                    <Paper variant="outlined" sx={{ p: 3, textAlign: 'center' }}>
                                        <Typography variant="body1" color="text.secondary" sx={{ mb: 2 }}>
                                            아직 구매 이력이 없습니다.
                                        </Typography>
                                        <Typography variant="body2" color="text.secondary">
                                            향후 고객의 상품 구매 내역이 이곳에 표시됩니다.
                                        </Typography>
                                    </Paper>
                                </Box>
                            )}
                        </DialogContent>
                        <DialogActions>
                            <Button onClick={() => setOpenDetailDialog(false)}>닫기</Button>
                            <Button onClick={() => {
                                setOpenDetailDialog(false);
                                handleEdit(selectedCustomer);
                            }} color="primary">
                                정보 수정
                            </Button>
                        </DialogActions>
                    </>
                )}
            </Dialog>
        </Box>
    );
};

export default CustomerList; 