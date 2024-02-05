import React,{useEffect} from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { ActivityIndicator, KeyboardAvoidingView, BackHandler, SafeAreaView, StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Platform } from "react-native";
import { useToast } from "react-native-toast-notifications";
import Button from './button';
import RNDateTimePicker from '@react-native-community/datetimepicker';
import './global.js';

export default function CreatePoll(props) {
    useFocusEffect(
        React.useCallback(() => {
            const onBackPress = () => {
                goback();
                return true;
            };

            BackHandler.addEventListener(
                'hardwareBackPress', onBackPress
            );

            return () =>
                BackHandler.removeEventListener(
                    'hardwareBackPress', onBackPress
                );
        }, [])
    );

    useEffect(() => {
        const requestOptions = {
            method: "GET",
            headers: {
                "LETSEAT-AUTHKEY": props.user.user_token,
                "Content-Type": "application/json"
            },
        };
          
        fetch(global.server + "feast_group/read.php?mine=1",requestOptions).then(res => res.json()).then(result => {
            setFeastGroups(result)
        }).then(() => {setLoadingName(false)})
    },[]);

    const styles = StyleSheet.create(
        Platform.select({
            ios: global.mainstyles,
            android: global.mainstyles,
            default: global.mainstyles
        })
    );


    const [feastGroups,setFeastGroups] = React.useState([]);
    const [feast_group,setFeastGroup] = React.useState(0);
    const [name, setName] = React.useState(props.user.name);
    const [notes, setNotes] = React.useState(props.user.name);
    const toast = useToast();
    const [isLoadingName, setLoadingName] = React.useState(false);
    // const [date, setDate] = React.useState(new Date());
    const notesRef = React.useRef(null);

    const [date, setDate] = React.useState(new Date())
    // const [date, setDate] = useState(new Date())
    const [times,setTimes] = React.useState([
        {id:5,name:"5min"},{id:10,name:"10min"},{id:15,name:"15min"},{id:30,name:"30min"},{id:60,name:"60min"}
    ])
    const [time,setTime] = React.useState(0);

    const createPoll = () => {
        setLoadingName(true);
        if (name !== '' && name !== null && name !== undefined) {
            const requestOptions = {
                method: "POST",
                headers: {
                    "LETSEAT-AUTHKEY": props.user.user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    name:name,
                    notes:notes,
                    time:time,
                    feast_group_id:feast_group
                }),
            };

            fetch(global.server + "poll/create.php", requestOptions).then(res => res.json()).then(result => {
                // console.log(result);
                if (result.result) {
                    alert("Poll Created Successfully!");
                    setLoadingName(false);
                    props.pagging(14);
                } else {
                    alert("Poll Not Created");
                    setLoadingName(false);
                }
            }).catch(() => {
                setTimeout(() => {
                    setLoadingName(false);
                    alert("Failed To Connect To Server");
                }, 2000);
            }).finally(() => {
                setTimeout(() => setLoadingName(false), 2000);
            });
        } else {
            toast.show("Please enter text into the field", {
                type: "warning",
                placement: "top",
                duration: 4000,
                offset: 100,
                animationType: "slide-in",
            });
            setLoadingName(false);
        }
    }

    const goback = () => {
        props.pagging(14)
    }

    const updateSelect = (id) => {
        setFeastGroup(id);
        setFeastGroups(feastGroups.map(Feast => Feast.id===id?{...Feast,selected:!Feast.selected}:{...Feast,selected:false}));
    }

    return (
        <SafeAreaView>
            <View style={styles.main}>
                <View style={styles.navbar}>
                    <View style={styles.backbuttonview}>
                        <Button
                            textstyle={styles.backbutton}
                            title="Back"
                            onPress={goback}>
                        </Button>
                    </View>
                </View>
                <View style={styles.scrollView}>
                    <KeyboardAvoidingView
                        behavior={Platform.OS === "ios" ? "height" : "padding"}
                        enabled>
                        <ScrollView>
                            <View
                                style={styles.signin}
                            >
                                <Text style={styles.dashboardheader}>
                                    Create Poll
                                </Text>
                                <Text>End Of Vote Time</Text>
                                {
                                    times.map(group => {
                                        return(
                                        <TouchableOpacity
                                            key={Math.random(50000)}
                                            id={true}
                                            name="notification"
                                            onPress={() => setTime(group.id)}
                                        >
                                            <View
                                            key={Math.random(50000)}
                                            style={{
                                                flexDirection: 'row'
                                            }}>
                                                <View
                                                key={Math.random(50000)} 
                                                style={{
                                                    margin: 5,
                                                    height: 24,
                                                    width: 24,
                                                    borderRadius: 12,
                                                    borderWidth: 2,
                                                    borderColor: '#000',
                                                    alignItems: 'center',
                                                    justifyContent: 'center',
                                                }}>
                                                    {
                                                        group.id === time ?
                                                            <View 
                                                            key={Math.random(50000)}
                                                            style={{
                                                                height: 12,
                                                                width: 12,
                                                                borderRadius: 6,
                                                                backgroundColor: '#000',
                                                            }} />
                                                            : null
                                                    }
                                                </View>
                                                <Text
                                            key={Math.random(50000)} style={{
                                                    color: "black",
                                                    fontSize: 20,
                                                    margin: 5
                                                }}
                                                >{group.name}</Text>
                                            </View>
                                        </TouchableOpacity>)
                                    })
                                }
                                <Text>Poll Name</Text>
                                <TextInput
                                    style={styles.input}
                                    onChangeText={setName}
                                    value={name}
                                    placeholder="Spur of the moment"
                                    onSubmitEditing={() => {
                                        notesRef.current.focus();
                                    }}
                                />
                                <Text>Poll Notes</Text>
                                <TextInput
                                    style={styles.input}
                                    onChangeText={setNotes}
                                    value={notes}
                                    placeholder="Extra comments"
                                    onSubmitEditing={() => {
                                        createPoll();
                                    }}
                                    ref={notesRef}
                                />
                                <Text>Select Feast Group</Text>
                                {
                                    feastGroups.map(group => {
                                        return(
                                        <TouchableOpacity
                                        key={Math.random(50000)}
                                            id={true}
                                            name="notification"
                                            onPress={() => updateSelect(group.id)}
                                        >
                                            <View
                                            key={Math.random(50000)}
                                            style={{
                                                flexDirection: 'row'
                                            }}>
                                                <View 
                                                key={Math.random(50000)}
                                                style={{
                                                    margin: 5,
                                                    height: 24,
                                                    width: 24,
                                                    borderRadius: 12,
                                                    borderWidth: 2,
                                                    borderColor: '#000',
                                                    alignItems: 'center',
                                                    justifyContent: 'center',
                                                }}>
                                                    {
                                                        group.selected ?
                                                            <View
                                                            key={Math.random(50000)}
                                                            style={{
                                                                height: 12,
                                                                width: 12,
                                                                borderRadius: 6,
                                                                backgroundColor: '#000',
                                                            }} />
                                                            : null
                                                    }
                                                </View>
                                                <Text
                                                key={Math.random(50000)}
                                                style={{
                                                    color: "black",
                                                    fontSize: 20,
                                                    margin: 5
                                                }}
                                                >{group.name}</Text>
                                            </View>
                                        </TouchableOpacity>)
                                    })
                                }
                                <Button
                                    onPress={() => {
                                        createPoll();
                                    }}
                                    title="Create Poll"
                                    // textstyle={styles.button}
                                    textstyle={styles.topbutton}
                                    accessibilityLabel="Create Poll"
                                    isLoading={isLoadingName}
                                />
                            </View>
                        </ScrollView>
                    </KeyboardAvoidingView>
                </View>
            </View>
        </SafeAreaView>

    )
}